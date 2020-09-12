using System.Collections.Generic;
using System.Threading.Tasks;

using Dysnomia.DehashMe.Common.Models;

namespace Dysnomia.DehashMe.Business {
	/// <summary>
	/// Interface used to expose business class used to get hashes
	/// </summary>
	public interface IHashService {
		Task<IEnumerable<SavedHash>> SearchByHash(string searchedHash);
		Task<IEnumerable<SavedHash>> SearchByText(string text);
		Task<int> Count();
	}
}
