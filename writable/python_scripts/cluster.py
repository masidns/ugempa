import sys
import pandas as pd
from sklearn.cluster import KMeans

def main(input_csv, n_clusters, cluster_by):
    try:
        # Mencetak jumlah cluster yang diterima
        print(f"Jumlah cluster yang diterima: {n_clusters}")
        # Membaca data dari file CSV
        print(f"Reading CSV file from: {input_csv}")

        data = pd.read_csv(input_csv)  # Membaca data gempa dari file CSV

        if cluster_by == 'depth':
            non_coordinate_columns = ['depth']
        elif cluster_by == 'magnitude':
            non_coordinate_columns = ['mag']
        elif cluster_by == 'depth_magnitude':
            non_coordinate_columns = ['depth', 'mag']
        else:
            raise ValueError("Invalid cluster_by value")

        print(f"Non-coordinate columns: {non_coordinate_columns}")

        X = data[non_coordinate_columns]  # Memilih kolom untuk clustering

        # Menjalankan KMeans dengan jumlah cluster yang ditentukan
        print(f"Running KMeans with n_clusters={n_clusters}")
        kmeans = KMeans(n_clusters=int(n_clusters), random_state=0).fit(X)  # Menggunakan KMeans untuk clustering

        data['cluster'] = kmeans.labels_  # Menambahkan hasil clustering ke kolom baru 'cluster'

        # Menyimpan data yang sudah dikelompokkan ke file CSV baru
        output_csv = input_csv.replace('.csv', '_clustered.csv')  # Mengubah nama file output
        data.to_csv(output_csv, index=False)  # Menyimpan data ke file CSV
        print(f"Clustered data saved to: {output_csv}")
    except Exception as e:
        print(f"Error: {str(e)}")  # Menampilkan pesan error jika terjadi kesalahan

if __name__ == "__main__":
    # Mengambil argumen dari command line
    input_csv = sys.argv[1]  # Path file CSV input
    n_clusters = sys.argv[2]  # Jumlah cluster yang diinginkan
    cluster_by = sys.argv[3]  # Metode clustering
    main(input_csv, n_clusters, cluster_by)  # Menjalankan fungsi utama dengan argumen yang diberikan
