import sys
import pandas as pd
import folium
import matplotlib.pyplot as plt
import matplotlib.colors as mcolors
from matplotlib.colors import ListedColormap

def main(input_csv_path, output_html_path, pre_clustered):
    try:
        # Membaca data dari file CSV
        print(f"Reading data from {input_csv_path}", file=sys.stderr)
        data = pd.read_csv(input_csv_path)  # Membaca data gempa dari file CSV
        print(f"Data read successfully: {data.head()}", file=sys.stderr)  # Menampilkan beberapa baris pertama data yang dibaca

        # Pastikan kolom 'cluster' ada di data
        if 'cluster' not in data.columns:
            raise ValueError("Kolom 'cluster' tidak ditemukan dalam data")

        # Debugging: Print unique cluster values
        print(f"Unique cluster values: {data['cluster'].unique()}", file=sys.stderr)

        # Definisikan warna untuk cluster menggunakan colormap 'viridis'
        cmap = plt.get_cmap('viridis')
        norm = plt.Normalize(vmin=data['cluster'].min(), vmax=data['cluster'].max())  # Normalisasi nilai cluster
        color_list = cmap(norm(data['cluster'].unique()))  # Mendapatkan daftar warna untuk setiap cluster
        cluster_colors = {cluster: mcolors.to_hex(color) for cluster, color in zip(data['cluster'].unique(), color_list)}  # Membuat dictionary cluster ke warna

        # Debugging: Print cluster colors
        print(f"Cluster colors: {cluster_colors}", file=sys.stderr)

        # Inisialisasi peta dengan lokasi awal di Indonesia dan zoom start 5
        print("Initializing map", file=sys.stderr)
        m = folium.Map(location=[-2.5489, 118.0149], zoom_start=5)  # Koordinat Indonesia

        if pre_clustered.lower() == 'true':
            # Jika data sudah dikelompokkan, gunakan warna sesuai cluster
            for _, row in data.iterrows():
                # Debugging: Print row cluster and color
                cluster = row['cluster']
                color = cluster_colors.get(cluster, 'blue')  # Default to blue if cluster not found
                print(f"Row cluster: {cluster}, Color: {color}", file=sys.stderr)
                
                folium.CircleMarker(
                    location=[row['lat'], row['lon']],  # Menentukan lokasi titik berdasarkan latitude dan longitude
                    radius=1,  # Menentukan radius lingkaran
                    popup=f"Tanggal: {row['tgl']}<br>Magnitude: {row['mag']}<br>Kedalaman: {row['depth']} km<br>{row['remark']}",  # Informasi popup
                    color=color,  # Warna garis lingkaran sesuai cluster
                    fill=True,  # Mengisi lingkaran dengan warna
                    fill_color=color  # Warna isian lingkaran sesuai cluster
                ).add_to(m)
        else:
            # Tambahkan titik-titik gempa ke peta dengan warna sesuai cluster
            for _, row in data.iterrows():
                # Debugging: Print row cluster and color
                cluster = row['cluster']
                color = cluster_colors.get(cluster, 'blue')  # Default to blue if cluster not found
                print(f"Row cluster: {cluster}, Color: {color}", file=sys.stderr)
                
                folium.CircleMarker(
                    location=[row['lat'], row['lon']],  # Menentukan lokasi titik berdasarkan latitude dan longitude
                    radius=1,  # Menentukan radius lingkaran
                    popup=f"Tanggal: {row['tgl']}<br>Magnitude: {row['mag']}<br>Kedalaman: {row['depth']} km<br>{row['remark']}",  # Informasi popup
                    color=color,  # Warna garis lingkaran sesuai cluster
                    fill=True,  # Mengisi lingkaran dengan warna
                    fill_color=color  # Warna isian lingkaran sesuai cluster
                ).add_to(m)

        # Simpan peta ke file HTML
        print(f"Saving map to {output_html_path}", file=sys.stderr)
        m.save(output_html_path)  # Menyimpan peta dalam format HTML
    except Exception as e:
        print(f"Error: {str(e)}", file=sys.stderr)  # Menampilkan pesan error jika terjadi kesalahan

if __name__ == "__main__":
    # Mengambil argumen dari command line
    input_csv_path = sys.argv[1]  # Path file CSV input
    output_html_path = sys.argv[2]  # Path file HTML output
    pre_clustered = sys.argv[3]  # Flag apakah data sudah dikelompokkan atau belum
    main(input_csv_path, output_html_path, pre_clustered)  # Menjalankan fungsi utama dengan argumen yang diberikan