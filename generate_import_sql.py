import csv
import sys

def clean_date(d):
    # Format DD/MM/YYYY to YYYY-MM-DD
    if not d: return 'NULL'
    parts = d.split('/')
    if len(parts) == 3:
        return f"'{parts[2]}-{parts[1]}-{parts[0]}'"
    return 'NULL'

def escape(s):
    if not s: return 'NULL'
    return "'" + s.replace("'", "''").replace("\\", "\\\\") + "'"

def get_kabko_sql(name):
    if not name: return 'NULL'
    # Clean name: KAB. BANYUWANGI -> Kabupaten Banyuwangi
    clean = name.replace('KAB.', 'Kabupaten').replace('KOTA', 'Kota').title().strip()
    return f"(SELECT id FROM kabupaten_kota WHERE nama LIKE '{clean}%' LIMIT 1)"

print("Generating SQL...")

# Reading Book1.csv
rows = []
with open('Book1.csv', 'r', encoding='utf-8-sig') as f: # utf-8-sig usually handles BOM
    reader = csv.DictReader(f, delimiter=';')
    for row in reader:
        rows.append(row)

unique_niks = set()
sql_statements = []

# Table cleaning
sql_statements.append("TRUNCATE TABLE hafiz;") 

for row in rows:
    nik = row.get('NIK', '').strip()
    if not nik or nik in unique_niks: continue # Skip if NIK empty or duplicate in this file
    unique_niks.add(nik)

    # Status Logic
    tahun_lulus = row.get('TAHUN LULUS SELEKSI', '').strip()
    status = 'lulus' if tahun_lulus else 'tidak_lulus' # Assuming empty means not passed based on context

    # Check 'mengajar' boolean logic - derived from Place/Date existence
    tempat = row.get('TEMPAT MENGAJAR', '').strip()
    is_mengajar = 1 if tempat else 0

    # Map Fields
    nama = escape(row.get('NAMA', '').strip())
    nik_val = escape(nik)
    tempat_lahir = escape(row.get('TEMPAT LAHIR', '').strip())
    
    tgl_lahir = row.get('TANGGAL LAHIR NIK', '').strip()
    if not tgl_lahir: tgl_lahir = row.get('TANGGAL LAHIR MANUAL', '').strip()
    tanggal_lahir = clean_date(tgl_lahir)
    
    jk = escape(row.get('JENIS ELAMIN', '').strip()) # Typo in CSV header likely 'KELAMIN'
    if jk == "'L'": jk = "'L'"
    elif jk == "'P'": jk = "'P'"
    else: jk = "'L'" # Default

    alamat = f"{row.get('ALAMAT', '')} RT {row.get('RT', '')} RW {row.get('RW', '')}".strip()
    alamat_val = escape(alamat)

    desa = escape(row.get('DESA/KELURAHAN', '').strip())
    kecamatan = escape(row.get('KECAMATAN', '').strip())
    
    kabko_nik = row.get('KABUPATEN/KOTA NIK', '').strip()
    kabko_id_sql = get_kabko_sql(kabko_nik)
    
    # Note: Column name changed to 'sertifikat_tahfidz' (Lembaga...)
    sertifikat = escape(row.get('LEMBAGA YANG MENGELUARKAN SERTIFIKAT TAHFIZ', '').strip())
    
    tempat_mengajar = escape(tempat)
    tmt_mengajar = clean_date(row.get('TERHITUNG MULAI TANGGAL MENGAJAR', '').strip())
    
    no_hp = escape(row.get('TELEPON', '').strip())
    
    # Keterangan
    keterangan = escape(row.get('KETERANGAN', '').strip())

    # Build Insert
    # Assuming table has: user_id (NULL), nama, nik, tempat_lahir, tanggal_lahir, jenis_kelamin, 
    # alamat, desa_kelurahan, kecamatan, kabupaten_kota_id, no_hp, 
    # sertifikat_tahfidz, mengajar, tempat_mengajar, tmt_mengajar, status_kelulusan,
    # created_at, updated_at
    
    sql = f"""INSERT INTO hafiz (
        nama, nik, tempat_lahir, tanggal_lahir, jenis_kelamin, 
        alamat, desa_kelurahan, kecamatan, kabupaten_kota_id, no_hp, 
        sertifikat_tahfidz, mengajar, tempat_mengajar, tmt_mengajar, status_kelulusan, 
        tahun_lulus, keterangan, created_at, updated_at
    ) VALUES (
        {nama}, {nik_val}, {tempat_lahir}, {tanggal_lahir}, {jk},
        {alamat_val}, {desa}, {kecamatan}, {kabko_id_sql}, {no_hp},
        {sertifikat}, {is_mengajar}, {tempat_mengajar}, {tmt_mengajar}, '{status}',
        {escape(tahun_lulus)}, {keterangan}, NOW(), NOW()
    );"""
    
    sql_statements.append(sql)

with open('import_hafiz.sql', 'w', encoding='utf-8') as f:
    f.write('\n'.join(sql_statements))

print(f"Generated {len(sql_statements)} INSERT statements.")
