namespace Dysnomia.DehashMe.WebApp.Models {
	/// <summary>
	/// Class used to represent a error displayed to the client on production environnement
	/// </summary>
	public class ErrorViewModel {
		public string RequestId { get; set; }
		public bool ShowRequestId => !string.IsNullOrEmpty(RequestId);
	}
}
