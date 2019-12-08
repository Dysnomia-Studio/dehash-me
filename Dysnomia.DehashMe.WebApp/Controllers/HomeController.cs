using System;
using System.Diagnostics;
using System.Threading.Tasks;

using Dysnomia.DehashMe.Business;
using Dysnomia.DehashMe.WebApp.Models;

using Microsoft.AspNetCore.Http;
using Microsoft.AspNetCore.Mvc;

namespace Dysnomia.DehashMe.WebApp.Controllers {
	public class HomeController : Controller {
		private readonly IHashService hashService;

		public HomeController(IHashService hashService) {
			this.hashService = hashService;
		}

		private string GetIp() {
			return (HttpContext.Connection.RemoteIpAddress != null ? HttpContext.Connection.RemoteIpAddress.ToString() : "?");
		}

		private void SetSessionsVars() {
			HttpContext.Session.SetString("Ip", GetIp());
			HttpContext.Session.SetString("Time", DateTime.Now.ToLongDateString() + " " + DateTime.Now.ToLongTimeString());
		}

		private bool IsBot() {
			if (HttpContext.Session.GetString("Time") == null) { return true; }

			var time = DateTime.Parse(
				HttpContext.Session.GetString("Time")
			);
			time.AddSeconds(2);

			return HttpContext.Session.GetString("Ip") != GetIp() ||
				(time > DateTime.Now);
		}

		[HttpGet]
		[Route("/")]
		public IActionResult Index() {
			SetSessionsVars();

			return View();
		}

		[HttpPost]
		[Route("/")]
		public async Task<IActionResult> Index(string hash, string dehash, string searchText) {
			ViewData["Result"] = null;

			if (!IsBot() && !string.IsNullOrWhiteSpace(searchText)) {
				ViewData["SearchText"] = searchText;

				if (hash != null) {
					ViewData["Result_Head"] = "Hash";
					ViewData["Result"] = await hashService.SearchByText(searchText);
				}

				if (dehash != null) {
					ViewData["Result_Head"] = "Text";
					ViewData["Result"] = await hashService.SearchByHash(searchText.ToLower());
				}
			}

			SetSessionsVars();

			return View();
		}

		[HttpGet]
		[Route("/count")]
		public async Task<int> Count() {
			return await hashService.Count();
		}

		[ResponseCache(Duration = 0, Location = ResponseCacheLocation.None, NoStore = true)]
		public IActionResult Error() {
			return View(new ErrorViewModel { RequestId = Activity.Current?.Id ?? HttpContext.TraceIdentifier });
		}
	}
}
