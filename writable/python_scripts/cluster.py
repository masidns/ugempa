import sys
import pandas as pd
from sklearn.cluster import KMeans

def main(input_csv, n_clusters):
    try:
        print(f"Jumlah cluster yang diterima: {n_clusters}")
        print(f"Reading CSV file from: {input_csv}")

        data = pd.read_csv(input_csv)
        non_coordinate_columns = ['depth', 'mag']

        print(f"Non-coordinate columns: {non_coordinate_columns}")

        X = data[non_coordinate_columns]

        print(f"Running KMeans with n_clusters={n_clusters}")
        kmeans = KMeans(n_clusters=int(n_clusters), random_state=0).fit(X)

        data['cluster'] = kmeans.labels_

        output_csv = input_csv.replace('.csv', '_clustered.csv')
        data.to_csv(output_csv, index=False)
        print(f"Clustered data saved to: {output_csv}")
    except Exception as e:
        print(f"Error: {str(e)}")

if __name__ == "__main__":
    input_csv = sys.argv[1]
    n_clusters = sys.argv[2]
    main(input_csv, n_clusters)
