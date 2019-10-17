using System.Collections.Generic;
using System.Threading.Tasks;

namespace Dysnomia.DehashMe.Business {
	public interface IHashService {
		Task<Dictionary<string, string>> SearchByHash(string searchedHash);
		Task<Dictionary<string, string>> SearchByText(string text);
		Task<int> Count();
	}
}
