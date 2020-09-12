using System;
using System.Collections.Generic;
using System.Data;

using Dysnomia.Common.SQL;

namespace Dysnomia.DehashMe.Common.Models {
	/// <summary>
	/// Representation of an hash
	/// </summary>
	public class SavedHash {
		/// <summary>
		/// Hash type: md5, sha512, ripemd, etc.
		/// </summary>
		public string Type { get; set; }

		/// <summary>
		/// Hash original text
		/// </summary>
		public string Text { get; set; }

		/// <summary>
		/// Hash result
		/// </summary>
		public string Hash { get; set; }

		/// <summary>
		/// Database reader to a collection containing this object
		/// </summary>
		public static HashSet<SavedHash> MapListFromReader(IDataReader reader) {
			HashSet<SavedHash> list = new HashSet<SavedHash>();

			while (reader.Read()) {
				list.Add(MapFromReader(reader));
			}

			return list;
		}

		/// <summary>
		/// Database reader to this object
		/// </summary>
		public static SavedHash MapFromReader(IDataReader reader) {
			var savedHash = new SavedHash {
				Hash = reader.GetString("hash"),
				Type = reader.GetString("type"),
				Text = reader.GetString("text")
			};

			return savedHash;
		}

		/// <summary>
		/// Override GetHashCode() to get same code if content is identical
		/// </summary>
		public override int GetHashCode() {
			return StringComparer.InvariantCulture.GetHashCode(Text) + StringComparer.InvariantCulture.GetHashCode(Type);
		}

		/// <summary>
		/// Override Equals() to check if two SavedHash are the same
		/// </summary>
		public override bool Equals(object obj) {
			return Text.Equals(((SavedHash)obj).Text) && Type.Equals(((SavedHash)obj).Type);
		}
	}
}
