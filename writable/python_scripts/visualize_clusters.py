import sys
import pandas as pd
import matplotlib.pyplot as plt
import base64
from io import BytesIO

def main(csv_file_path):
    try:
        # Baca data hasil clustering
        print(f"Reading CSV file from: {csv_file_path}", file=sys.stderr)
        data = pd.read_csv(csv_file_path)
        print(f"Data read successfully: {data.head()}", file=sys.stderr)
        
        # Plotting
        plt.figure(figsize=(10, 6))
        scatter = plt.scatter(data['depth'], data['mag'], c=data['cluster'], cmap='viridis')
        plt.colorbar(scatter, label='Cluster')
        plt.xlabel('Depth')
        plt.ylabel('Magnitude')
        plt.title('Clustering of Earthquake Data')

        # Simpan gambar ke dalam buffer
        buffer = BytesIO()
        plt.savefig(buffer, format='png')
        plt.close()
        buffer.seek(0)

        # Encode gambar ke base64
        img_str = base64.b64encode(buffer.read()).decode('utf-8')
        print(img_str)
    except Exception as e:
        print(f"Error: {str(e)}", file=sys.stderr)

if __name__ == "__main__":
    csv_file_path = sys.argv[1]
    main(csv_file_path)
