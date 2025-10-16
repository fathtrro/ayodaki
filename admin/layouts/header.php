<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../unauthorized.php');
    exit();
}
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background-color: #f5f7fa;
            display: flex;
            overflow-x: hidden;
        }

        .sidebar {
            width: 250px;
            background-color: #1e293b;
            color: #fff;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            height: 100vh;
            position: fixed;
        }

        .sidebar h4 {
            font-size: 1.3rem;
            padding: 1.2rem;
            text-align: center;
            background-color: #111827;
            font-weight: bold;
            margin: 0;
        }

        .sidebar a {
            color: #cbd5e1;
            padding: 12px 20px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: background 0.2s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: #0f766e;
            color: white;
        }

        .topbar {
            position: fixed;
            left: 250px;
            right: 0;
            height: 60px;
            background: #fff;
            border-bottom: 1px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            z-index: 1000;
        }

        .main-content {
            margin-left: 250px;
            margin-top: 70px;
            padding: 2rem;
            flex-grow: 1;
        }

        .card-stats {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s;
        }

        .card-stats:hover {
            transform: translateY(-3px);
        }

        .card-stats i {
            font-size: 2rem;
            color: #198754;
        }
    </style>
</head>

<body>