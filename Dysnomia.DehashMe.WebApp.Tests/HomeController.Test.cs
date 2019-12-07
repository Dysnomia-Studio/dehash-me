using System.Net;
using System.Net.Http;

using FluentAssertions;

using Microsoft.AspNetCore.Hosting;
using Microsoft.AspNetCore.TestHost;

using Xunit;


namespace Dysnomia.DehashMe.WebApp.Tests {
	public class HomeController {
		public HttpClient client { get; }

		public HomeController() {
			var builder = new WebHostBuilder().UseStartup<Startup>();
			var server = new TestServer(builder);

			client = server.CreateClient();
		}

		[Fact]
		public async void ShouldGet200_GET_Index() {
			var response = await client.GetAsync("/");

			response.StatusCode.Should().Be(HttpStatusCode.OK);
		}

		[Fact]
		public async void ShouldGet200_POST_EmptySearch_Index() {
			var response = await client.PostAsync("/",
				new StringContent("{ searchText: \"\" }")
			);

			response.StatusCode.Should().Be(HttpStatusCode.OK);
		}

		[Fact]
		public async void ShouldGet200_POST_WhitespaceSearch_Index() {
			var response = await client.PostAsync("/",
				new StringContent("{ searchText: \"     \" }")
			);

			response.StatusCode.Should().Be(HttpStatusCode.OK);
		}

		[Fact]
		public async void ShouldGet200_POST_Hash_Index() {
			var response = await client.PostAsync("/",
				new StringContent("{ searchText: \"test\", hash: \"hash\" }")
			);

			response.StatusCode.Should().Be(HttpStatusCode.OK);
		}

		[Fact]
		public async void ShouldGet200_POST_Dehash_Index() {
			var response = await client.PostAsync("/",
				new StringContent("{ searchText: \"test\", dehash: \"dehash\" }")
			);

			response.StatusCode.Should().Be(HttpStatusCode.OK);
		}
	}
}
