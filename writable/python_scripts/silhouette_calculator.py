import sys
import json
from sklearn.metrics import silhouette_score
from sklearn.cluster import KMeans
import pandas as pd

def load_data(csv_path, cluster_by):
    try:
        data = pd.read_csv(csv_path)
        if cluster_by == 'depth':
            features = data[['depth']]
        elif cluster_by == 'magnitude':
            features = data[['mag']]
        elif cluster_by == 'depth_magnitude':
            features = data[['depth', 'mag']]
        print(f"Data loaded: {features.head()}", file=sys.stderr)  # Tambahkan log untuk melihat data yang dimuat
        return features
    except Exception as e:
        print(json.dumps({"error": str(e)}))
        return None

def calculate_silhouette(csv_path, cluster_by, max_clusters):
    data = load_data(csv_path, cluster_by)
    results = {}
    if data is not None:
        print(f"Data loaded for silhouette calculation:\n{data.head()}", file=sys.stderr)  # Tambahkan log untuk melihat data yang dimuat
        for n_clusters in range(2, max_clusters + 1):
            kmeans = KMeans(n_clusters=n_clusters)  # Menggunakan jumlah cluster yang berbeda
            labels = kmeans.fit_predict(data)
            score = silhouette_score(data, labels)
            results[f'Cluster {n_clusters}'] = score
            print(f"Silhouette Score for {n_clusters} clusters: {score}", file=sys.stderr)  # Tambahkan log untuk melihat hasil perhitungan
        return results
    else:
        return {'error': 'No data available'}

if __name__ == "__main__":
    try:
        csv_path = sys.argv[1]
        cluster_by = sys.argv[2]
        max_clusters = int(sys.argv[3])
        result = calculate_silhouette(csv_path, cluster_by, max_clusters)
        # Cetak output JSON terakhir
        print(json.dumps(result))
    except Exception as e:
        print(json.dumps({"error": str(e)}))
