import sys
import pandas as pd
import matplotlib.pyplot as plt
import base64
from io import BytesIO
from categorize import categorize_by_magnitude, categorize_by_depth

def categorize_cluster(data, cluster_by, n_clusters):
    if cluster_by == 'magnitude':
        return categorize_by_magnitude(data, n_clusters)
    elif cluster_by == 'depth':
        return categorize_by_depth(data, n_clusters)
    else:
        return [f'Kategori {i + 1}' for i in data['cluster']]

def is_base64(sb):
    try:
        if isinstance(sb, str):
            sb_bytes = bytes(sb, 'ascii')
        elif isinstance(sb, bytes):
            sb_bytes = sb
        else:
            raise ValueError("Argument must be string or bytes")
        return base64.b64encode(base64.b64decode(sb_bytes)) == sb_bytes
    except Exception:
        return False

def main(csv_file_path, cluster_by, year, year_range=None):
    try:
        sys.stdout = sys.stderr
        
        print("Reading CSV file...")
        data = pd.read_csv(csv_file_path)
        print("Data read successfully")
        
        n_clusters = data['cluster'].nunique()
        print(f"Number of clusters: {n_clusters}")
        
        data['category'] = categorize_cluster(data, cluster_by, n_clusters)
        print("Data categorized successfully")
        
        print("Generating plot...")
        plt.figure(figsize=(10, 6))
        scatter = plt.scatter(data['lon'], data['lat'], c=data['cluster'], cmap='viridis', edgecolor='k')
        plt.xlabel('Longitude')
        plt.ylabel('Latitude')
        
        if year == 'all' and year_range:
            title = f'Clustering of Earthquake Data by {cluster_by.capitalize()} ({year_range[0]} - {year_range[1]})'
        else:
            title = f'Clustering of Earthquake Data by {cluster_by.capitalize()} ({year})'
        
        plt.colorbar(scatter, label='Cluster')
        plt.title(title)

        print("Adding legend to plot...")
        cluster_counts = data['cluster'].value_counts().sort_index()
        category_mapping = data.groupby('cluster')['category'].first()
        legend_labels = [f'Cluster {i} ({cluster_counts[i]}) - {category_mapping[i]}' for i in cluster_counts.index]
        handles = [plt.Line2D([0], [0], marker='o', color='w', markerfacecolor=scatter.cmap(scatter.norm(i)), markersize=10) for i in cluster_counts.index]
        plt.legend(handles, legend_labels, title='Cluster', bbox_to_anchor=(1.2, 1), loc='upper left', borderaxespad=0.)

        print("Saving plot to buffer...")
        buffer = BytesIO()
        plt.savefig(buffer, format='png', bbox_inches='tight')
        buffer.seek(0)

        print("Encoding plot to base64...")
        img_str = base64.b64encode(buffer.read()).decode('utf-8')
        
        print("Base64 string generated")
        
        sys.stdout = sys.__stdout__

        return img_str
    except Exception as e:
        sys.stdout = sys.__stdout__
        print(f"Error: {str(e)}", file=sys.stderr)
        return None

if __name__ == "__main__":
    csv_file_path = sys.argv[1]
    cluster_by = sys.argv[2]
    year = sys.argv[3]
    year_range = None
    if year == 'all' and len(sys.argv) > 4:
        year_range = (sys.argv[4], sys.argv[5])
    img_str = main(csv_file_path, cluster_by, year, year_range)
    
    if img_str and is_base64(img_str):
        print(img_str)
    else:
        print("Error: Generated string is not valid base64.")
