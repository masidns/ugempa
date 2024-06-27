import sys
import pandas as pd
from sklearn.cluster import KMeans
import json

def main(csv_file_path, n_clusters):
    try:
        print(f"Reading CSV file from: {csv_file_path}", file=sys.stderr)
        data = pd.read_csv(csv_file_path)
        print(f"Data read successfully: {data.head()}", file=sys.stderr)
        
        print(f"Performing KMeans clustering with {n_clusters} clusters.", file=sys.stderr)
        kmeans = KMeans(n_clusters=n_clusters)
        kmeans.fit(data[['depth', 'mag']])
        data['cluster'] = kmeans.labels_
        
        result = data.to_json(orient='records')
        print(result)
    except Exception as e:
        print(f"Error: {str(e)}", file=sys.stderr)

if __name__ == "__main__":
    csv_file_path = sys.argv[1]
    n_clusters = int(sys.argv[2])
    main(csv_file_path, n_clusters)
