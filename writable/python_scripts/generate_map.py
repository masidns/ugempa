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

        # Inisialisasi peta dengan lokasi awal di [0, 0] dan zoom start 2
        print("Initializing map", file=sys.stderr)
        m = folium.Map(location=[0, 0], zoom_start=2)

        if pre_clustered.lower() == 'true':
            # Jika data belum dikelompokkan, gunakan warna biru default untuk semua titik
            for _, row in data.iterrows():
                folium.CircleMarker(
                    location=[row['lat'], row['lon']],  # Menentukan lokasi titik berdasarkan latitude dan longitude
                    radius=5,  # Menentukan radius lingkaran
                    popup=f"Tanggal: {row['tgl']}<br>Magnitude: {row['mag']}<br>Kedalaman: {row['depth']} km<br>{row['remark']}",  # Informasi popup
                    color='blue',  # Warna garis lingkaran
                    fill=True,  # Mengisi lingkaran dengan warna
                    fill_color='blue'  # Warna isian lingkaran
                ).add_to(m)
        else:
            # Definisikan warna untuk cluster menggunakan colormap 'viridis'
            cmap = plt.get_cmap('viridis')
            norm = plt.Normalize(vmin=data['cluster'].min(), vmax=data['cluster'].max())  # Normalisasi nilai cluster
            color_list = cmap(norm(data['cluster'].unique()))  # Mendapatkan daftar warna untuk setiap cluster
            cluster_colors = {cluster: color for cluster, color in zip(data['cluster'].unique(), color_list)}  # Membuat dictionary cluster ke warna

            # Tambahkan titik-titik gempa ke peta dengan warna sesuai cluster
            print("Adding earthquake points to map", file=sys.stderr)
            for _, row in data.iterrows():
                folium.CircleMarker(
                    location=[row['lat'], row['lon']],  # Menentukan lokasi titik berdasarkan latitude dan longitude
                    radius=5,  # Menentukan radius lingkaran
                    popup=f"Tanggal: {row['tgl']}<br>Magnitude: {row['mag']}<br>Kedalaman: {row['depth']} km<br>{row['remark']}",  # Informasi popup
                    color=mcolors.to_hex(cluster_colors[row['cluster']]),  # Warna garis lingkaran sesuai cluster
                    fill=True,  # Mengisi lingkaran dengan warna
                    fill_color=mcolors.to_hex(cluster_colors[row['cluster']])  # Warna isian lingkaran sesuai cluster
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
