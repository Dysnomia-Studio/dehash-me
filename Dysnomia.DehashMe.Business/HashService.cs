using System;
using System.Collections.Generic;
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

		public async Task<Dictionary<string, string>> SearchByHash(string searchedHash) {
			// TODO: multiples hashes

			var hashes = await hashDataAccess.SearchByHash(searchedHash);

			var dico = new Dictionary<string, string>();
			foreach (var hash in hashes) {
				if (dico.ContainsKey(hash.Type)) { continue; }

				dico.Add(
					hash.Type,
					hash.Text
				);
			}

			return dico;
		}

		public HashSet<SavedHash> GenerateHashes(string text) {
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
		}

		public async Task<Dictionary<string, string>> SearchByText(string text) {
			// TODO: multiples texts

			var alreadySavedResults = await hashDataAccess.SearchByText(text);

			var generatedHashes = GenerateHashes(text);
			generatedHashes.ExceptWith(alreadySavedResults);

			hashDataAccess.InsertAll(generatedHashes);

			alreadySavedResults.UnionWith(generatedHashes);

			var dico = new Dictionary<string, string>();
			foreach (var hash in alreadySavedResults) {
				dico.Add(
					hash.Type,
					hash.Hash
				);
			}

			return dico;
		}

		public async Task<int> Count() {
			return await hashDataAccess.Count();
		}
	}
}
