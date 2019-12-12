using System;
using System.Collections.Generic;
using System.Data;

using Dysnomia.Common.SQL;

namespace Dysnomia.DehashMe.Common.Models {
	public class SavedHash {
		public string Type { get; set; }
		public string Text { get; set; }
		public string Hash { get; set; }

		public static HashSet<SavedHash> MapListFromReader(IDataReader reader) {
			HashSet<SavedHash> list = new HashSet<SavedHash>();

			while(reader.Read()) {
				list.Add(MapFromReader(reader));
			}

			return list;
		}

		public static SavedHash MapFromReader(IDataReader reader) {
			var savedHash = new SavedHash {
				Hash = reader.GetString("hash"),
				Type = reader.GetString("type"),
				Text = reader.GetString("text")
			};

			return savedHash;
		}

		public override int GetHashCode() {
			return StringComparer.InvariantCulture.GetHashCode(Type);
		}

		public override bool Equals(object obj) {
			return Type.Equals(((SavedHash) obj).Type);
		}
	}
}
