<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Your Hamster</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex flex-col items-center justify-center min-h-screen bg-gray-100">
    <h1 class="text-2xl font-bold mb-4">Customize Your Hamster</h1>

    <div class="flex flex-col items-center bg-white p-6 rounded-lg shadow-lg">
        <object id="hamsterPreview" type="image/svg+xml" data="./assets/svg/hamster.svg" width="480"
            height="480"></object>

        <label class="mt-4">Color 1</label>
        <input type="color" id="color1" class="border p-1 rounded" value="#ffcc00">

        <label class="mt-2">Color 2</label>
        <input type="color" id="color2" class="border p-1 rounded" value="#d4a500">

        <button id="saveHamster" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded">Save Hamster</button>
    </div>

    <script>
        function darkenColor(hex, factor = 0.8) {
            let r = parseInt(hex.substring(1, 3), 16) * factor;
            let g = parseInt(hex.substring(3, 5), 16) * factor;
            let b = parseInt(hex.substring(5, 7), 16) * factor;
            return `rgb(${Math.floor(r)}, ${Math.floor(g)}, ${Math.floor(b)})`;
        }

        document.getElementById("hamsterPreview").addEventListener("load", function () {
            const svgDoc = this.contentDocument;

            document.getElementById("color1").addEventListener("input", function () {
                let color1 = this.value;
                let shadowColor1 = darkenColor(color1);
                ["buttColor", "faceColor", "earColor"].forEach(id => {
                    let el = svgDoc.getElementById(id);
                    if (el) el.setAttribute("fill", color1);
                });
                ["buttS", "faceS", "earS"].forEach(id => {
                    let el = svgDoc.getElementById(id);
                    if (el) el.setAttribute("fill", shadowColor1);
                });
            });

            document.getElementById("color2").addEventListener("input", function () {
                let color2 = this.value;
                let shadowColor2 = darkenColor(color2);
                ["chestColor", "earFluff"].forEach(id => {
                    let el = svgDoc.getElementById(id);
                    if (el) el.setAttribute("fill", color2);
                });
                ["chestS"].forEach(id => {
                    let el = svgDoc.getElementById(id);
                    if (el) el.setAttribute("fill", shadowColor2);
                });
            });
        });

        document.getElementById("saveHamster").addEventListener("click", function () {
            const color1 = document.getElementById("color1").value;
            const color2 = document.getElementById("color2").value;
            const shadow1 = darkenColor(color1);
            const shadow2 = darkenColor(color2);

            fetch("api/save_hamster.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ color1, color2, shadow1, shadow2 })
            }).then(res => res.json()).then(data => {
                if (data.success) {
                    alert("Hamster saved!");
                    window.location.href = "index.php";
                } else {
                    alert("Failed to save hamster");
                }
            });
        });
    </script>
</body>

</html>