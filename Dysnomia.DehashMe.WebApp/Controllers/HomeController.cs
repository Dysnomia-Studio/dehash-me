using System;
using System.Diagnostics;
using System.Threading.Tasks;

using Dysnomia.Common.Security;
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

		[HttpGet]
		[Route("/")]
		public IActionResult Index() {
			BotHelper.SetSessionsVars(HttpContext);

			return View();
		}

		[HttpPost]
		[Route("/")]
		public async Task<IActionResult> Index(string hash, string dehash, string searchText) {
			ViewData["Result"] = null;

			if (!BotHelper.IsBot(HttpContext) && !string.IsNullOrWhiteSpace(searchText)) {
				string newSearchText = searchText.Trim().Replace("\u0000", "");

				ViewData["SearchText"] = newSearchText;

				if (hash != null) {
					ViewData["Result"] = await hashService.SearchByText(newSearchText);
				}

				if (dehash != null) {
					ViewData["Result"] = await hashService.SearchByHash(newSearchText.ToLower());
				}
			}

			BotHelper.SetSessionsVars(HttpContext);

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
