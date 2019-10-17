using System;

namespace Dysnomia.DehashMe.WebApp.Models {
	public class ErrorViewModel {
		public string RequestId { get; set; }

		public bool ShowRequestId => !string.IsNullOrEmpty(RequestId);
	}
}
