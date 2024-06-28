import sys
import pandas as pd
import folium
import matplotlib.pyplot as plt
import matplotlib.colors as mcolors
from matplotlib.colors import ListedColormap

def generate_map(csv_file_path, output_html_path, pre_clustered):
    try:
        # Membaca data dari file CSV
        print(f"Reading CSV file from: {csv_file_path}")
        data = pd.read_csv(csv_file_path)  # Membaca data gempa dari file CSV

        if data.empty:
            raise ValueError("Dataframe is empty")  # Menangani kasus di mana dataframe kosong

        # Membuat peta menggunakan Folium
        print("Creating map...")
        map_center = [data['lat'].mean(), data['lon'].mean()]  # Menentukan pusat peta berdasarkan rata-rata koordinat
        map_object = folium.Map(location=map_center, zoom_start=5)  # Menginisialisasi peta

        if pre_clustered == 'true':
            # Definisikan warna untuk pre-clustered
            colors = ['blue'] * len(data)  # Menentukan warna biru untuk semua titik
            data['color'] = colors  # Menambahkan kolom warna ke dataframe
        else:
            # Definisikan warna untuk post-clustered
            cmap = plt.get_cmap('viridis')  # Menggunakan colormap 'viridis'
            norm = plt.Normalize(vmin=data['cluster'].min(), vmax=data['cluster'].max())  # Normalisasi nilai cluster
            color_list = cmap(norm(data['cluster'].unique()))  # Mendapatkan daftar warna untuk setiap cluster
            cluster_colors = {cluster: color for cluster, color in zip(data['cluster'].unique(), color_list)}  # Membuat dictionary cluster ke warna
            data['color'] = data['cluster'].map(cluster_colors)  # Menambahkan kolom warna ke dataframe berdasarkan cluster

        for _, row in data.iterrows():
            folium.CircleMarker(
                location=[row['lat'], row['lon']],  # Menentukan lokasi titik berdasarkan latitude dan longitude
                radius=5,  # Menentukan radius lingkaran
                popup=row['remark'],  # Informasi popup
                color=mcolors.to_hex(row['color']),  # Warna garis lingkaran sesuai cluster
                fill=True,  # Mengisi lingkaran dengan warna
                fill_color=mcolors.to_hex(row['color'])  # Warna isian lingkaran sesuai cluster
            ).add_to(map_object)

        # Simpan peta ke file HTML
        print(f"Saving map to: {output_html_path}")
        map_object.save(output_html_path)  # Menyimpan peta dalam format HTML
        print(f"Peta berhasil disimpan di: {output_html_path}")
    except Exception as e:
        print(f"Error: {str(e)}")  # Menampilkan pesan error jika terjadi kesalahan
        sys.exit(1)  # Keluar dengan status 1 jika terjadi kesalahan

if __name__ == "__main__":
    # Mengambil argumen dari command line
    if len(sys.argv) != 4:
        print("Usage: python generate_map.py <input_csv_path> <output_html_path> <pre_clustered>")
        sys.exit(1)  # Keluar dengan status 1 jika jumlah argumen tidak sesuai

    input_csv_path = sys.argv[1]  # Path file CSV input
    output_html_path = sys.argv[2]  # Path file HTML output
    pre_clustered = sys.argv[3]  # Flag apakah data sudah dikelompokkan atau belum
    generate_map(input_csv_path, output_html_path, pre_clustered)  # Menjalankan fungsi utama dengan argumen yang diberikan
