<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Chamados</title>
    <script src="../assets/js/custom/scripts/tailwindcss.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="../assets/js/custom/scripts/jquery-3.7.1.min.js"></script>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <script src="../assets/js/custom/scripts/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/material-components-web.min.css">
    <script src="../assets/js/custom/dataTables/dataTables.js"></script>
    <script src="../assets/js/custom/dataTables/dataTables.material.js"></script>

    <script src="../assets/js/custom/scripts/imask.js"></script>
    <script src="../assets/js/custom/scripts/sweetalert2.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lightgallery@2.7.1/css/lightgallery-bundle.min.css" />
    <script src="../assets/js/custom/scripts/lightgallery.min.js"></script>
    <script src="../assets/js/custom/scripts/lg-zoom.min.js"></script>
    <script src="../assets/js/custom/scripts/lg-thumbnail.min.js"></script>

    <!-- PDF.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>
    <link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />

    <!-- FilePond script -->
    <script src="../assets/js/custom/scripts/filepond.js"></script>


    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #6b73ff 0%, #000dff 100%);
        }

        .ticket-card {
            transition: all 0.3s ease;
        }

        .ticket-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .file-preview {
            max-width: 100px;
            max-height: 100px;
            object-fit: cover;
            border-radius: 5px;
            margin-right: 10px;
            margin-bottom: 10px;
        }

        .sidebar {
            transition: all 0.3s ease;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                position: absolute;
                z-index: 50;
                height: 100vh;
            }

            .sidebar.open {
                transform: translateX(0);
            }
        }
    </style>

</head>