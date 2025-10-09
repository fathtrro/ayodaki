<?php
declare(strict_types=1);
header('Content-Type: text/html; charset=UTF-8');

$teman = [
    ['nama' => 'Andi',  'umur' => 25, 'hobi' => ['Futsal', 'Membaca']],
    ['nama' => 'alkhea',  'umur' => 24, 'hobi' => ['Ngoding', 'Bersepeda']],
    ['nama' => 'marsha', 'umur' => 23, 'hobi' => ['Memasak', 'Fotografi']],
    ['nama' => 'dani',  'umur' => 26, 'hobi' => ['Menulis', 'Yoga']],
    ['nama' => 'daffa',   'umur' => 27, 'hobi' => ['Musik', 'Game']],
];

function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Daftar Teman</title>
  <style>
    body { font-family: system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif; margin: 24px; }
    table { border-collapse: collapse; width: 100%; max-width: 720px; }
    th, td { border: 1px solid #ccc; padding: 8px 12px; }
    thead th { background: #f4f4f4; text-align: left; }
    tbody tr:nth-child(even) { background: #fafafa; }
  </style>
</head>
<body>
  <h1>Daftar Teman</h1>
  <table>
    <thead>
      <tr>
        <th>Nama</th>
        <th>Umur</th>
        <th>Hobi</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($teman as $t): ?>
        <tr>
          <td><?= e((string)$t['nama']) ?></td>
          <td><?= (int)$t['umur'] ?></td>
          <td><?= e(is_array($t['hobi']) ? implode(', ', $t['hobi']) : (string)$t['hobi']) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</body>
</html>