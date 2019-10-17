using System.Collections.Generic;
using System.Threading.Tasks;

using Dysnomia.DehashMe.Common.Models;

namespace Dysnomia.DehashMe.DataAccess {
	public interface IHashDataAccess {
		Task<IEnumerable<SavedHash>> SearchByHash(string hash);
		Task<HashSet<SavedHash>> SearchByText(string text);
		void InsertAll(HashSet<SavedHash> hashes);
		Task<int> Count();
	}
}
