using Dysnomia.DehashMe.Business;
using Dysnomia.DehashMe.Common;
using Dysnomia.DehashMe.DataAccess;

using Microsoft.AspNetCore.Builder;
using Microsoft.AspNetCore.Hosting;
using Microsoft.AspNetCore.Http;
using Microsoft.Extensions.Configuration;
using Microsoft.Extensions.DependencyInjection;
using Microsoft.Extensions.Hosting;

using System;
using System.Globalization;
using System.Threading;

namespace Dysnomia.DehashMe.WebApp {
    public class Startup {
        public Startup(IConfiguration configuration) {
            Configuration = configuration;
        }

        public IConfiguration Configuration { get; }

        private string GetConnectionString() {
            var host = Environment.GetEnvironmentVariable("PG_HOST");
            var username = Environment.GetEnvironmentVariable("PG_USER");
            var password = Environment.GetEnvironmentVariable("PG_PASSWORD");
            var database = Environment.GetEnvironmentVariable("PG_DB");

            return $"Host={host};Database={database};Username={username};Password={password}";
        }

        // This method gets called by the runtime. Use this method to add services to the container.
        public void ConfigureServices(IServiceCollection services) {
            services.Configure<AppSettings>(x => x.ConnectionString = GetConnectionString());

            services.Configure<CookiePolicyOptions>(options => {
                // This lambda determines whether user consent for non-essential 
                // cookies is needed for a given request.
                options.CheckConsentNeeded = context => true;
                options.MinimumSameSitePolicy = SameSiteMode.Strict;
            });

            services.AddTransient<IHashDataAccess, HashDataAccess>();
            services.AddTransient<IHashService, HashService>();

            services.AddControllersWithViews();
            services.AddMemoryCache();
            services.AddSession(options => {
                // Set a short timeout for easy testing.
                options.IdleTimeout = TimeSpan.FromMinutes(60);
                // You might want to only set the application cookies over a secure connection:
                options.Cookie.SecurePolicy = CookieSecurePolicy.SameAsRequest;
                options.Cookie.SameSite = SameSiteMode.Strict;
                options.Cookie.HttpOnly = true;
                // Make the session cookie essential
                options.Cookie.IsEssential = true;

                options.Cookie.MaxAge = TimeSpan.FromMinutes(60);
            });
        }

        // This method gets called by the runtime. Use this method to configure the HTTP request pipeline.
        public void Configure(IApplicationBuilder app, IWebHostEnvironment env) {
            if (env.IsDevelopment() || env.IsEnvironment("Testing")) {
                app.UseDeveloperExceptionPage();
            } else {
                app.UseExceptionHandler("/Home/Error");
            }

            app.UseHttpsRedirection();
            app.UseStaticFiles();
            app.UseRouting();
            app.UseCookiePolicy();

            app.UseAuthorization();

            app.UseSession();

            if (env.IsEnvironment("Testing")) {
                app.Use(async (context, next) => {
                    if (!context.Request.Query.ContainsKey("bot") || context.Request.Query["bot"] != "true") {
                        context.Session.SetString("Ip", "?");

                        var date = DateTime.Now;
                        date.AddSeconds(-5);
                        Thread.CurrentThread.CurrentCulture = CultureInfo.InvariantCulture; // Fix for french windows client for Unit Tests
                        context.Session.SetString("Time", date.ToLongDateString() + " " + date.ToLongTimeString());
                    }

                    await next();
                });
            }

            app.UseEndpoints(endpoints => {
                endpoints.MapControllerRoute(
                    name: "default",
                    pattern: "{controller=Home}/{action=Index}/{id?}");
            });
        }
    }
}
