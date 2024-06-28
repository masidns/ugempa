import sys
import pandas as pd
from sklearn.cluster import KMeans
from sklearn.preprocessing import StandardScaler
import json

def main(csv_file_path, n_clusters):
    try:
        # Baca data
        data = pd.read_csv(csv_file_path)
        
        # Praproses: Menghapus baris dengan nilai yang hilang
        data.dropna(inplace=True)
        
        # Identifikasi kolom non-koordinat untuk normalisasi
        non_coordinate_columns = data.columns.difference(['lat', 'lon', 'tgl', 'remark'])
        print(f"Non-coordinate columns: {non_coordinate_columns}", file=sys.stderr)
        
        # Praproses: Normalisasi data untuk semua kolom non-koordinat
        scaler = StandardScaler()
        data[non_coordinate_columns] = scaler.fit_transform(data[non_coordinate_columns])
        
        # Clustering menggunakan semua kolom
        kmeans = KMeans(n_clusters=n_clusters)
        kmeans.fit(data[non_coordinate_columns])
        data['cluster'] = kmeans.labels_
        
        # Mengembalikan hasil clustering sebagai JSON
        result = data.to_json(orient='records')
        print(result)
    except Exception as e:
        print(f"Error: {str(e)}", file=sys.stderr)

if __name__ == "__main__":
    csv_file_path = sys.argv[1]
    n_clusters = int(sys.argv[2])
    main(csv_file_path, n_clusters)
