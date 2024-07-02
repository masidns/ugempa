import sys
import pandas as pd
import matplotlib.pyplot as plt
import base64
from io import BytesIO

def main(csv_file_path, cluster_by, year, year_range=None):
    try:
        # Baca data hasil clustering
        print(f"Reading CSV file from: {csv_file_path}", file=sys.stderr)
        data = pd.read_csv(csv_file_path)
        print(f"Data read successfully: {data.head()}", file=sys.stderr)
        
        # Plotting
        plt.figure(figsize=(10, 6))
        if cluster_by == 'depth':
            scatter = plt.scatter(data['lon'], data['lat'], c=data['cluster'], cmap='viridis', edgecolor='k')
            plt.xlabel('Longitude')
            plt.ylabel('Latitude')
            title = f'Clustering of Earthquake Data by Depth ({year})'
        elif cluster_by == 'magnitude':
            scatter = plt.scatter(data['lon'], data['lat'], c=data['cluster'], cmap='viridis', edgecolor='k')
            plt.xlabel('Longitude')
            plt.ylabel('Latitude')
            title = f'Clustering of Earthquake Data by Magnitude ({year})'
        elif cluster_by == 'depth & magnitude':
            scatter = plt.scatter(data['lon'], data['lat'], c=data['cluster'], cmap='viridis', edgecolor='k')
            plt.xlabel('Longitude')
            plt.ylabel('Latitude')
            title = f'Clustering of Earthquake Data by Depth and Magnitude ({year})'
        else:
            raise ValueError("Invalid cluster_by value")
        
        if year == 'all' and year_range:
            title = f'Clustering of Earthquake Data by {cluster_by.capitalize()} ({year_range[0]} - {year_range[1]})'
        
        plt.colorbar(scatter, label='Cluster')
        plt.title(title)

        # Menambahkan informasi total data di setiap cluster pada legenda
        cluster_counts = data['cluster'].value_counts().sort_index()
        legend_labels = [f'Cluster {i} ({count})' for i, count in cluster_counts.items()]
        handles = [plt.Line2D([0], [0], marker='o', color='w', markerfacecolor=scatter.cmap(scatter.norm(i)), markersize=10) for i in cluster_counts.index]
        plt.legend(handles, legend_labels, title='Cluster', bbox_to_anchor=(1.2, 1), loc='upper left', borderaxespad=0.)

        # Simpan gambar ke dalam buffer
        buffer = BytesIO()
        plt.savefig(buffer, format='png', bbox_inches='tight')
        plt.close()
        buffer.seek(0)

        # Encode gambar ke base64
        img_str = base64.b64encode(buffer.read()).decode('utf-8')
        print(img_str)
    except Exception as e:
        print(f"Error: {str(e)}", file=sys.stderr)

if __name__ == "__main__":
    csv_file_path = sys.argv[1]
    cluster_by = sys.argv[2]
    year = sys.argv[3]
    year_range = None
    if year == 'all' and len(sys.argv) > 4:
        year_range = (sys.argv[4], sys.argv[5])
    main(csv_file_path, cluster_by, year, year_range)
