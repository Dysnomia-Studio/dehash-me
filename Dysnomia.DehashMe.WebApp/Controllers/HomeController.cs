using System.Diagnostics;
using System.Threading.Tasks;

using Dysnomia.DehashMe.Business;
using Dysnomia.DehashMe.WebApp.Models;

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
			return View();
		}

		[HttpPost]
		[Route("/")]
		public async Task<IActionResult> Index(string hash, string dehash, string searchText) {
			ViewData["Result"] = null;

			// TODO: anti-bot system

			if (!string.IsNullOrWhiteSpace(searchText)) {
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
