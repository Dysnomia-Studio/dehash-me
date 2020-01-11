using System.Collections.Generic;
using System.Threading.Tasks;

namespace Dysnomia.DehashMe.Business {
	/// <summary>
	/// Interface used to expose business class used to get hashes
	/// </summary>
	public interface IHashService {
		Task<Dictionary<string, string>> SearchByHash(string searchedHash);
		Task<Dictionary<string, string>> SearchByText(string text);
		Task<int> Count();
	}
}
