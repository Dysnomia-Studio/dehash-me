using System;
using System.Collections.Generic;
using System.Linq;
using System.Security.Cryptography;
using System.Text;
using System.Threading.Tasks;

using Dysnomia.DehashMe.Common.Models;
using Dysnomia.DehashMe.DataAccess;

namespace Dysnomia.DehashMe.Business {
	public class HashService : IHashService {
		private readonly IHashDataAccess hashDataAccess;

		private readonly static Dictionary<string, Type> hashAlgorithms = new Dictionary<string, Type>() {
			{ "md5", typeof(MD5CryptoServiceProvider) },
			{ "sha1", typeof(SHA1CryptoServiceProvider) },
			{ "sha256", typeof(SHA256CryptoServiceProvider) },
			{ "sha384", typeof(SHA384CryptoServiceProvider) },
			{ "sha512", typeof(SHA512CryptoServiceProvider) }
		};

		public HashService(IHashDataAccess hashDataAccess) {
			this.hashDataAccess = hashDataAccess;
		}

		public async Task<IEnumerable<SavedHash>> SearchByHash(string searchedHash) {
			try {
				List<string> lines = searchedHash.Split(
					new[] { "\r\n", "\r", "\n" },
					StringSplitOptions.None
				).Where((elt) => !string.IsNullOrWhiteSpace(elt)).ToList();

				while (lines.Count > 5) {
					lines.RemoveAt(5);
				}

				IEnumerable<SavedHash> hashes = new List<SavedHash>();
				foreach (var hash in lines) {
					hashes = hashes.Concat(await hashDataAccess.SearchByHash(hash));
				}

				return hashes;
			} catch (Exception e) {
				Console.WriteLine(e.Message);
				Console.WriteLine(e.StackTrace);
			}

			return null;
		}

		public HashSet<SavedHash> GenerateHashes(string text) {
			try {
				var generatedHashes = new HashSet<SavedHash>();

				foreach (var kvp in hashAlgorithms) {
					HashAlgorithm hashAlgorithm = (HashAlgorithm)Activator.CreateInstance(kvp.Value);

					generatedHashes.Add(new SavedHash() {
						Text = text,
						Type = kvp.Key,
						Hash = BitConverter.ToString(
								hashAlgorithm.ComputeHash(Encoding.UTF8.GetBytes(text))
							).Replace("-", string.Empty).ToLower()
					});
				}

				return generatedHashes;
			} catch (Exception e) {
				Console.WriteLine(e.Message);
				Console.WriteLine(e.StackTrace);
			}

			return null;
		}

		public async Task<IEnumerable<SavedHash>> SearchByText(string text) {
			try {
				List<string> lines = text.Split(
					new[] { "\r\n", "\r", "\n" },
					StringSplitOptions.None
				).Where((elt) => !string.IsNullOrWhiteSpace(elt) && elt.Length < 100_000).ToList(); // Too large string might break database index

				while (lines.Count > 5) {
					lines.RemoveAt(5);
				}

				var alreadySavedResults = new HashSet<SavedHash>();

				foreach (var currText in lines) {
					alreadySavedResults.UnionWith(await hashDataAccess.SearchByText(currText));

					var generatedHashes = GenerateHashes(currText);
					generatedHashes.ExceptWith(alreadySavedResults);

					hashDataAccess.InsertAll(generatedHashes);

					alreadySavedResults.UnionWith(generatedHashes);
				}

				return alreadySavedResults;
			} catch (Exception e) {
				Console.WriteLine(e.Message);
				Console.WriteLine(e.StackTrace);
			}

			return null;
		}

		public async Task<int> Count() {
			try {
				return await hashDataAccess.Count();
			} catch (Exception e) {
				Console.WriteLine(e.Message);
				Console.WriteLine(e.StackTrace);
			}

			return 0;
		}
	}
}
