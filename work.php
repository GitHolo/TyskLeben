<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <title>Work</title>
</head>

<body>
    <div class="flex flex-col items-center">
        <h1 class="text-2xl font-bold">🏪 Work at 6-12</h1>
        <button id="workShift" class="mt-4 px-4 py-2 bg-green-500 text-white rounded">Complete Shift (+$10)</button>
    </div>

    <script>
        document.getElementById("workShift").addEventListener("click", () => {
            fetch("api/work.php", { method: "POST" })
                .then(res => res.json())
                .then(data => {
                    alert("You earned $10!");
                    window.location.href = "index.php"; // Back home after shift
                });
        });
    </script>

</body>

</html>