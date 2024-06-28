import sys
import pandas as pd
import folium
import matplotlib.pyplot as plt
import matplotlib.colors as mcolors
from matplotlib.colors import ListedColormap

def main(input_csv_path, output_html_path, pre_clustered):
    try:
        print(f"Reading data from {input_csv_path}", file=sys.stderr)
        # Baca data gempa
        data = pd.read_csv(input_csv_path)
        print(f"Data read successfully: {data.head()}", file=sys.stderr)

        # Inisialisasi peta
        print("Initializing map", file=sys.stderr)
        m = folium.Map(location=[0, 0], zoom_start=2)

        if pre_clustered.lower() == 'true':
            # Jika data belum dikelompokkan, gunakan warna default
            for _, row in data.iterrows():
                folium.CircleMarker(
                    location=[row['lat'], row['lon']],
                    radius=5,
                    popup=f"Tanggal: {row['tgl']}<br>Magnitude: {row['mag']}<br>Kedalaman: {row['depth']} km<br>{row['remark']}",
                    color='blue',
                    fill=True,
                    fill_color='blue'
                ).add_to(m)
        else:
            # Definisikan warna untuk cluster
            cmap = plt.get_cmap('viridis')
            norm = plt.Normalize(vmin=data['cluster'].min(), vmax=data['cluster'].max())
            color_list = cmap(norm(data['cluster'].unique()))
            cluster_colors = {cluster: color for cluster, color in zip(data['cluster'].unique(), color_list)}

            # Tambahkan titik-titik gempa ke peta
            print("Adding earthquake points to map", file=sys.stderr)
            for _, row in data.iterrows():
                folium.CircleMarker(
                    location=[row['lat'], row['lon']],
                    radius=5,
                    popup=f"Tanggal: {row['tgl']}<br>Magnitude: {row['mag']}<br>Kedalaman: {row['depth']} km<br>{row['remark']}",
                    color=mcolors.to_hex(cluster_colors[row['cluster']]),
                    fill=True,
                    fill_color=mcolors.to_hex(cluster_colors[row['cluster']])
                ).add_to(m)

        # Simpan peta ke file HTML
        print(f"Saving map to {output_html_path}", file=sys.stderr)
        m.save(output_html_path)
    except Exception as e:
        print(f"Error: {str(e)}", file=sys.stderr)

if __name__ == "__main__":
    input_csv_path = sys.argv[1]
    output_html_path = sys.argv[2]
    pre_clustered = sys.argv[3]
    main(input_csv_path, output_html_path, pre_clustered)
