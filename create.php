<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Your Hamster</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex flex-col items-center justify-center min-h-screen bg-gray-100">
    <h1 class="text-2xl font-bold mb-4">Create Your Hamster</h1>

    <div class="flex flex-col items-center bg-white p-6 rounded-lg shadow-lg">
        <?php include './assets/hamster.html'; ?>

        <label class="mt-4">Color 1</label>
        <input type="color" id="color1" class="border p-1 rounded" value="#ffcc00">

        <label class="mt-2">Color 2</label>
        <input type="color" id="color2" class="border p-1 rounded" value="#d4a500">

        <button id="saveHamster" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded">Save Hamster</button>
    </div>

    <script>
        document.getElementById("color1").addEventListener("input", function () {
            document.querySelectorAll("circle")[0].setAttribute("fill", this.value);
            document.querySelectorAll("circle")[1].setAttribute("fill", this.value);
            document.querySelectorAll("circle")[2].setAttribute("fill", this.value);
        });

        document.getElementById("color2").addEventListener("input", function () {
            document.querySelectorAll("circle")[0].setAttribute("stroke", this.value);
            document.querySelectorAll("circle")[1].setAttribute("stroke", this.value);
            document.querySelectorAll("circle")[2].setAttribute("stroke", this.value);
        });

        document.getElementById("saveHamster").addEventListener("click", function () {
            const color1 = document.getElementById("color1").value;
            const color2 = document.getElementById("color2").value;

            fetch("api/save_hamster.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ color1, color2 })
            }).then(res => res.json()).then(data => {
                if (data.success) {
                    alert("Hamster saved!");
                    window.location.href = "index.php";
                }
            });
        });
    </script>
</body>

</html>