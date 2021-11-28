using Dysnomia.Common.SQL;
using Dysnomia.DehashMe.Common;
using Dysnomia.DehashMe.Common.Models;

using Microsoft.Extensions.Options;

using Npgsql;

using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace Dysnomia.DehashMe.DataAccess {
	public class HashDataAccess : IHashDataAccess {
		private readonly string connectionString;

		public HashDataAccess(IOptions<AppSettings> appSettings) {
			connectionString = appSettings.Value.ConnectionString;
		}

		public async Task<IEnumerable<SavedHash>> SearchByHash(string hash) {
			using var connection = new NpgsqlConnection(connectionString);

			var reader = await connection.ExecuteQuery(
				"SELECT * FROM \"hashLists\" WHERE hash=@hash",
				new Dictionary<string, object>() {
									{ "hash", hash }
				}
			);

			return SavedHash.MapListFromReader(reader);
		}

		public async Task<HashSet<SavedHash>> SearchByText(string text) {
			using var connection = new NpgsqlConnection(connectionString);

			var reader = await connection.ExecuteQuery(
				"SELECT * FROM \"hashLists\" WHERE text=@text",
				new Dictionary<string, object>() {
						{ "text", text }
				}
			);

			return SavedHash.MapListFromReader(reader);

		}

		public async void InsertAll(HashSet<SavedHash> hashes) {
			if (!hashes.Any()) { return; }

			using var connection = new NpgsqlConnection(connectionString);

			var query = new StringBuilder("INSERT INTO public.\"hashLists\"(type, text, hash) VALUES");

			var parameters = new Dictionary<string, object>();

			int i = 0;
			foreach (var hash in hashes) {
				query.Append("(@" + i + "_type, @" + i + "_text, @" + i + "_hash),");

				parameters.Add("@" + i + "_type", hash.Type);
				parameters.Add("@" + i + "_text", hash.Text);
				parameters.Add("@" + i + "_hash", hash.Hash);

				i++;
			}

			query.Remove(query.Length - 1, 1); /// on supprime la dernière virgule

			await connection.ExecuteNonQuery(query.ToString(), parameters);
		}

		public async Task<int> Count() {
			using var connection = new NpgsqlConnection(connectionString);

			var reader = await connection.ExecuteQuery(
				"SELECT reltuples::BIGINT AS approximate_row_count FROM pg_class WHERE relname = 'hashLists'"
			);

			reader.Read();

			return reader.GetInt("approximate_row_count");
		}
	}
}
