using FluentAssertions;

using Microsoft.AspNetCore.Hosting;
using Microsoft.AspNetCore.TestHost;
using Microsoft.Extensions.Configuration;

using System;
using System.Collections.Generic;
using System.Net;
using System.Net.Http;
using System.Threading.Tasks;

using Xunit;


namespace Dysnomia.DehashMe.WebApp.Tests {
	public class HomeController {
		public HttpClient client { get; }
		public TestServer server { get; }

		public HomeController() {
			var config = new ConfigurationBuilder()
				.AddJsonFile("appsettings.json", optional: false)
				.AddUserSecrets<Startup>(optional: true)
				.Build();

			var builder = new WebHostBuilder()
				.UseConfiguration(config)
				.UseStartup<Startup>()
				.UseEnvironment("Testing");
			server = new TestServer(builder);

			client = server.CreateClient();
		}

		[Fact]
		public async Task ShouldGet200_GET_Index() {
			var response = await client.GetAsync("/");

			response.StatusCode.Should().Be(HttpStatusCode.OK);
		}

		[Fact]
		public async Task ShouldGet200_POST_Bot_Index() {
			var response = await client.PostAsync("/", new FormUrlEncodedContent(new Dictionary<string, string> {
				{ "searchText", "test" },
				{ "hash", "hash" }
			}));

			response.StatusCode.Should().Be(HttpStatusCode.OK);
		}


		[Fact]
		public async Task ShouldGet200_POST_EmptySearch_Index() {
			var response = await client.PostAsync("/", new FormUrlEncodedContent(new Dictionary<string, string> {
				{ "searchText", "" },
			}));

			response.StatusCode.Should().Be(HttpStatusCode.OK);
		}

		[Fact]
		public async Task ShouldGet200_POST_WhitespaceSearch_Index() {
			var response = await client.PostAsync("/", new FormUrlEncodedContent(new Dictionary<string, string> {
				{ "searchText", "     " },
			}));

			response.StatusCode.Should().Be(HttpStatusCode.OK);
		}

		[Fact]
		public async Task ShouldGet200_POST_Hash_New() {
			var response = await client.PostAsync("/", new FormUrlEncodedContent(new Dictionary<string, string> {
				{ "searchText", DateTime.Now.ToLongDateString() + DateTime.Now.ToLongTimeString() },
				{ "hash", "hash" }
			}));

			response.StatusCode.Should().Be(HttpStatusCode.OK);
		}

		[Fact]
		public async Task ShouldGet200_POST_Dehash_Index() {
			var response = await client.PostAsync("/", new FormUrlEncodedContent(new Dictionary<string, string> {
				{ "searchText", "test" },
				{ "dehash", "dehash" }
			}));

			response.StatusCode.Should().Be(HttpStatusCode.OK);
		}

		[Fact]
		public async Task ShouldGet200_GET_Count() {
			var response = await client.GetAsync("/count");

			response.StatusCode.Should().Be(HttpStatusCode.OK);
		}
	}
}
