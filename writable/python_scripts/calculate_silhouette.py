import sys
import pandas as pd
from sklearn.metrics import silhouette_score
from sklearn.cluster import KMeans

def main():
    if len(sys.argv) != 4:
        print("Usage: python calculate_silhouette.py <csv_path> <n_clusters> <cluster_by>")
        return

    # print(f"Arguments received: {sys.argv}")  # Komentari atau hapus baris ini

    csv_path = sys.argv[1]
    n_clusters = int(sys.argv[2])
    cluster_by = sys.argv[3]

    data = pd.read_csv(csv_path)

    if cluster_by == 'depth':
        X = data[['depth']]
    elif cluster_by == 'magnitude':
        X = data[['mag']]
    elif cluster_by == 'depth_magnitude':
        X = data[['depth', 'mag']]
    else:
        print("Invalid cluster_by value")
        return

    kmeans = KMeans(n_clusters=n_clusters, random_state=42)  # Tetapkan random_state untuk konsistensi
    labels = kmeans.fit_predict(X)

    score = silhouette_score(X, labels)
    print(score)

if __name__ == "__main__":
    main()