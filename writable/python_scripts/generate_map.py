import sys
import pandas as pd
import folium
import matplotlib.pyplot as plt
import matplotlib.colors as mcolors
from matplotlib.colors import ListedColormap

def generate_map(csv_file_path, output_html_path, pre_clustered):
    try:
        print(f"Reading CSV file from: {csv_file_path}")
        data = pd.read_csv(csv_file_path)

        if data.empty:
            raise ValueError("Dataframe is empty")

        print("Creating map...")
        map_center = [data['lat'].mean(), data['lon'].mean()]
        map_object = folium.Map(location=map_center, zoom_start=5)

        if pre_clustered == 'true':
            # Definisikan warna untuk pre-clustered
            colors = ['blue'] * len(data)
            data['color'] = colors
        else:
            # Definisikan warna untuk post-clustered
            cmap = plt.get_cmap('viridis')
            norm = plt.Normalize(vmin=data['cluster'].min(), vmax=data['cluster'].max())
            color_list = cmap(norm(data['cluster'].unique()))
            cluster_colors = {cluster: color for cluster, color in zip(data['cluster'].unique(), color_list)}
            data['color'] = data['cluster'].map(cluster_colors)

        for _, row in data.iterrows():
            folium.CircleMarker(
                location=[row['lat'], row['lon']],
                radius=5,
                popup=row['remark'],
                color=mcolors.to_hex(row['color']),
                fill=True,
                fill_color=mcolors.to_hex(row['color'])
            ).add_to(map_object)

        print(f"Saving map to: {output_html_path}")
        map_object.save(output_html_path)
        print(f"Peta berhasil disimpan di: {output_html_path}")
    except Exception as e:
        print(f"Error: {str(e)}")
        sys.exit(1)

if __name__ == "__main__":
    if len(sys.argv) != 4:
        print("Usage: python generate_map.py <input_csv_path> <output_html_path> <pre_clustered>")
        sys.exit(1)

    input_csv_path = sys.argv[1]
    output_html_path = sys.argv[2]
    pre_clustered = sys.argv[3]
    generate_map(input_csv_path, output_html_path, pre_clustered)
