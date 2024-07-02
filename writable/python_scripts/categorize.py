import pandas as pd

def categorize_by_magnitude(data, n_clusters):
    categories = []
    quantiles = data['mag'].quantile([i / n_clusters for i in range(n_clusters + 1)]).values
    category_labels = {
        2: ['Magnitudo Rendah', 'Magnitudo Tinggi'],
        3: ['Magnitudo Rendah', 'Magnitudo Sedang', 'Magnitudo Tinggi'],
        4: ['Magnitudo Sangat Rendah', 'Magnitudo Rendah', 'Magnitudo Sedang', 'Magnitudo Tinggi'],
        5: ['Magnitudo Sangat Rendah', 'Magnitudo Rendah', 'Magnitudo Sedang-Rendah', 'Magnitudo Sedang-Tinggi', 'Magnitudo Tinggi'],
        6: ['Magnitudo Sangat Rendah', 'Magnitudo Rendah', 'Magnitudo Rendah-Sedang', 'Magnitudo Sedang', 'Magnitudo Menengah-Tinggi', 'Magnitudo Tinggi'],
        7: ['Magnitudo Sangat Rendah', 'Magnitudo Rendah', 'Magnitudo Rendah-Sedang', 'Magnitudo Sedang', 'Magnitudo Menengah-Tinggi', 'Magnitudo Tinggi', 'Magnitudo Sangat Tinggi'],
        8: ['Magnitudo Sangat Rendah', 'Magnitudo Rendah', 'Magnitudo Rendah-Sedang', 'Magnitudo Sedang-Rendah', 'Magnitudo Sedang-Tinggi', 'Magnitudo Tinggi', 'Magnitudo Sangat Tinggi', 'Magnitudo Ekstrim'],
        9: ['Magnitudo Sangat Rendah', 'Magnitudo Rendah', 'Magnitudo Rendah-Sedang', 'Magnitudo Sedang-Rendah', 'Magnitudo Sedang', 'Magnitudo Menengah-Tinggi', 'Magnitudo Tinggi', 'Magnitudo Sangat Tinggi', 'Magnitudo Ekstrim']
    }
    labels = category_labels.get(n_clusters, [f'Kategori {i + 1}' for i in range(n_clusters)])
    for index, row in data.iterrows():
        categorized = False
        for i in range(n_clusters):
            if quantiles[i] <= row['mag'] < quantiles[i + 1]:
                categories.append(labels[i])
                categorized = True
                break
        if not categorized:
            print(f"Data point at index {index} with magnitude {row['mag']} did not fit into any category, assigning to last category.")
            categories.append(labels[-1])
    if len(categories) != len(data):
        print(f"Length of categories ({len(categories)}) does not match length of data ({len(data)})")
        raise ValueError(f"Length of categories ({len(categories)}) does not match length of data ({len(data)})")
    return categories

def categorize_by_depth(data, n_clusters):
    categories = []
    quantiles = data['depth'].quantile([i / n_clusters for i in range(n_clusters + 1)]).values
    category_labels = {
        2: ['Kedalaman Dangkal', 'Kedalaman Dalam'],
        3: ['Kedalaman Dangkal', 'Kedalaman Menengah', 'Kedalaman Dalam'],
        4: ['Kedalaman Sangat Dangkal', 'Kedalaman Dangkal', 'Kedalaman Menengah', 'Kedalaman Dalam'],
        5: ['Kedalaman Sangat Dangkal', 'Kedalaman Dangkal', 'Kedalaman Sedang-Dangkal', 'Kedalaman Sedang-Dalam', 'Kedalaman Dalam'],
        6: ['Kedalaman Sangat Dangkal', 'Kedalaman Dangkal', 'Kedalaman Sedang-Dangkal', 'Kedalaman Menengah', 'Kedalaman Sedang-Dalam', 'Kedalaman Dalam'],
        7: ['Kedalaman Sangat Dangkal', 'Kedalaman Dangkal', 'Kedalaman Sedang-Dangkal', 'Kedalaman Menengah', 'Kedalaman Sedang-Dalam', 'Kedalaman Dalam', 'Kedalaman Sangat Dalam'],
        8: ['Kedalaman Sangat Dangkal', 'Kedalaman Dangkal', 'Kedalaman Sedang-Dangkal', 'Kedalaman Menengah-Dangkal', 'Kedalaman Menengah-Dalam', 'Kedalaman Sedang-Dalam', 'Kedalaman Dalam', 'Kedalaman Sangat Dalam'],
        9: ['Kedalaman Sangat Dangkal', 'Kedalaman Dangkal', 'Kedalaman Sedang-Dangkal', 'Kedalaman Menengah-Dangkal', 'Kedalaman Menengah', 'Kedalaman Menengah-Dalam', 'Kedalaman Sedang-Dalam', 'Kedalaman Dalam', 'Kedalaman Sangat Dalam']
    }
    labels = category_labels.get(n_clusters, [f'Kategori {i + 1}' for i in range(n_clusters)])
    for index, row in data.iterrows():
        categorized = False
        for i in range(n_clusters):
            if quantiles[i] <= row['depth'] < quantiles[i + 1]:
                categories.append(labels[i])
                categorized = True
                break
        if not categorized:
            print(f"Data point at index {index} with depth {row['depth']} did not fit into any category, assigning to last category.")
            categories.append(labels[-1])
    if len(categories) != len(data):
        print(f"Length of categories ({len(categories)}) does not match length of data ({len(data)})")
        raise ValueError(f"Length of categories ({len(categories)}) does not match length of data ({len(data)})")
    return categories
