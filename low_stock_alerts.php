<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sticky Alert</title>
    <script src="https://cdn.tailwindcss.com"></script> <!-- Tailwind CSS CDN -->
    <style>
        /* Custom Styles for the Sticky Alert */
        .sticky-alert {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 15px;
            border-radius: 5px;
            font-size: 16px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 9999;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 300px;
        }

        .sticky-alert .close-btn {
            background: none;
            border: none;
            font-size: 20px;
            color: #721c24;
            cursor: pointer;
            padding: 0;
        }
        
        .sticky-alert .close-btn:hover {
            color: #f5c6cb;
        }

        /* Optional: Animation for showing and hiding alert */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .sticky-alert {
            animation: fadeIn 0.3s ease-in-out;
        }
    </style>
</head>
<body class="bg-gray-100 flex justify-center items-center min-h-screen">

    <!-- Sticky Alert HTML -->
    <div id="stickyAlert" class="sticky-alert">
        <span>This is a sticky alert message!</span>
        <button class="close-btn" onclick="closeAlert()">×</button>
    </div>

    <script>
        // Function to close the sticky alert
        function closeAlert() {
            document.getElementById('stickyAlert').style.display = 'none';
        }

        // Optional: Automatically hide after 5 seconds (remove this line if not needed)
        setTimeout(function() {
            closeAlert();
        }, 5000);
    </script>

</body>
</html>
