import pandas as pd

def categorize_by_magnitude(data, n_clusters):
    if n_clusters == 2:
        labels = ['Magnitudo Rendah', 'Magnitudo Tinggi']
    elif n_clusters == 3:
        labels = ['Magnitudo Rendah', 'Magnitudo Sedang', 'Magnitudo Tinggi']
    elif n_clusters == 4:
        labels = ['Magnitudo Sangat Rendah', 'Magnitudo Rendah', 'Magnitudo Sedang', 'Magnitudo Tinggi']
    elif n_clusters == 5:
        labels = ['Magnitudo Sangat Rendah', 'Magnitudo Rendah', 'Magnitudo Sedang-Rendah', 'Magnitudo Sedang-Tinggi', 'Magnitudo Tinggi']
    elif n_clusters == 6:
        labels = ['Magnitudo Sangat Rendah', 'Magnitudo Rendah', 'Magnitudo Rendah-Sedang', 'Magnitudo Sedang', 'Magnitudo Menengah-Tinggi', 'Magnitudo Tinggi']
    elif n_clusters == 7:
        labels = ['Magnitudo Sangat Rendah', 'Magnitudo Rendah', 'Magnitudo Rendah-Sedang', 'Magnitudo Sedang', 'Magnitudo Menengah-Tinggi', 'Magnitudo Tinggi', 'Magnitudo Sangat Tinggi']
    elif n_clusters == 8:
        labels = ['Magnitudo Sangat Rendah', 'Magnitudo Rendah', 'Magnitudo Rendah-Sedang', 'Magnitudo Sedang-Rendah', 'Magnitudo Sedang-Tinggi', 'Magnitudo Tinggi', 'Magnitudo Sangat Tinggi', 'Magnitudo Ekstrim']
    elif n_clusters == 9:
        labels = ['Magnitudo Sangat Rendah', 'Magnitudo Rendah', 'Magnitudo Rendah-Sedang', 'Magnitudo Sedang-Rendah', 'Magnitudo Sedang', 'Magnitudo Menengah-Tinggi', 'Magnitudo Tinggi', 'Magnitudo Sangat Tinggi', 'Magnitudo Ekstrim']
    else:
        labels = [f'Kategori {i + 1}' for i in range(n_clusters)]
    
    data['category'] = data['cluster'].apply(lambda x: labels[x])
    return data['category']

def categorize_by_depth(data, n_clusters):
    if n_clusters == 2:
        labels = ['Kedalaman Dangkal', 'Kedalaman Dalam']
    elif n_clusters == 3:
        labels = ['Kedalaman Dangkal', 'Kedalaman Menengah', 'Kedalaman Dalam']
    elif n_clusters == 4:
        labels = ['Kedalaman Sangat Dangkal', 'Kedalaman Dangkal', 'Kedalaman Menengah', 'Kedalaman Dalam']
    elif n_clusters == 5:
        labels = ['Kedalaman Sangat Dangkal', 'Kedalaman Dangkal', 'Kedalaman Sedang-Dangkal', 'Kedalaman Sedang-Dalam', 'Kedalaman Dalam']
    elif n_clusters == 6:
        labels = ['Kedalaman Sangat Dangkal', 'Kedalaman Dangkal', 'Kedalaman Sedang-Dangkal', 'Kedalaman Menengah', 'Kedalaman Sedang-Dalam', 'Kedalaman Dalam']
    elif n_clusters == 7:
        labels = ['Kedalaman Sangat Dangkal', 'Kedalaman Dangkal', 'Kedalaman Sedang-Dangkal', 'Kedalaman Menengah', 'Kedalaman Sedang-Dalam', 'Kedalaman Dalam', 'Kedalaman Sangat Dalam']
    elif n_clusters == 8:
        labels = ['Kedalaman Sangat Dangkal', 'Kedalaman Dangkal', 'Kedalaman Sedang-Dangkal', 'Kedalaman Menengah-Dangkal', 'Kedalaman Menengah-Dalam', 'Kedalaman Sedang-Dalam', 'Kedalaman Dalam', 'Kedalaman Sangat Dalam']
    elif n_clusters == 9:
        labels = ['Kedalaman Sangat Dangkal', 'Kedalaman Dangkal', 'Kedalaman Sedang-Dangkal', 'Kedalaman Menengah-Dangkal', 'Kedalaman Menengah', 'Kedalaman Menengah-Dalam', 'Kedalaman Sedang-Dalam', 'Kedalaman Dalam', 'Kedalaman Sangat Dalam']
    else:
        labels = [f'Kategori {i + 1}' for i in range(n_clusters)]
    
    data['category'] = data['cluster'].apply(lambda x: labels[x])
    return data['category']
