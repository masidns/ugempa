import sys
import pandas as pd
import folium
import matplotlib.pyplot as plt
import matplotlib.colors as mcolors
import random

def generate_map(csv_file_path, output_html_path, pre_clustered=False):
    try:
        print(f"Reading CSV file from: {csv_file_path}")
        data = pd.read_csv(csv_file_path)

        if data.empty:
            raise ValueError("Dataframe is empty")

        print("Creating map...")
        map_center = [data['lat'].mean(), data['lon'].mean()]
        map_object = folium.Map(location=map_center, zoom_start=5)
        
        if pre_clustered:
            # Definisikan warna acak dari palet warna yang sama
            cmap = plt.get_cmap('viridis')
            num_points = len(data)
            colors = [mcolors.to_hex(cmap(i / num_points)) for i in range(num_points)]
            for idx, row in data.iterrows():
                folium.CircleMarker(
                    location=[row['lat'], row['lon']],
                    radius=5,
                    popup=f"Tanggal: {row['tgl']}<br>Magnitude: {row['mag']}<br>Kedalaman: {row['depth']} km<br>{row['remark']}",
                    color=colors[idx],
                    fill=True,
                    fill_color=colors[idx]
                ).add_to(map_object)
        else:
            # Definisikan warna untuk cluster
            cmap = plt.get_cmap('viridis')
            norm = plt.Normalize(vmin=data['cluster'].min(), vmax=data['cluster'].max())
            color_list = cmap(norm(data['cluster'].unique()))
            cluster_colors = {cluster: mcolors.to_hex(color) for cluster, color in zip(data['cluster'].unique(), color_list)}

            for _, row in data.iterrows():
                folium.CircleMarker(
                    location=[row['lat'], row['lon']],
                    radius=5,
                    popup=f"Tanggal: {row['tgl']}<br>Magnitude: {row['mag']}<br>Kedalaman: {row['depth']} km<br>{row['remark']}",
                    color=cluster_colors[row['cluster']],
                    fill=True,
                    fill_color=cluster_colors[row['cluster']]
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
    pre_clustered = sys.argv[3].lower() == 'true'
    generate_map(input_csv_path, output_html_path, pre_clustered)
