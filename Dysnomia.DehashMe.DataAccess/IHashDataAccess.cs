using System.Collections.Generic;
using System.Threading.Tasks;

using Dysnomia.DehashMe.Common.Models;

namespace Dysnomia.DehashMe.DataAccess {
	/// <summary>
	/// Interface used to expose data access class used to get hashes
	/// 
	/// This tier must be called by the services, not by the controller.
	/// </summary>
	public interface IHashDataAccess {
		Task<IEnumerable<SavedHash>> SearchByHash(string hash);
		Task<HashSet<SavedHash>> SearchByText(string text);
		void InsertAll(HashSet<SavedHash> hashes);
		Task<int> Count();
	}
}
