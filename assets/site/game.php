<div id="customerArea"
    class="mb-5 bg-[url(./assets/svg/store-bg.jpg)] bg-cover relative h-96 flex justify-center items-end bg-gray-200 overflow-hidden">

    <object id="counterSVG" type="image/svg+xml" data="./assets/svg/counter.svg" width="480" height="140"
        class="absolute z-10"></object>
    <div class="mt-6 flex justify-center">
        <object id="hamsterPreview" type="image/svg+xml" data="./assets/svg/hamster.svg" width="200"
            height="200"></object>
    </div>

    <script type="text/javascript">
        function darkenColor(hex, factor = 0.8) {
            let r = parseInt(hex.substring(1, 3), 16) * factor;
            let g = parseInt(hex.substring(3, 5), 16) * factor;
            let b = parseInt(hex.substring(5, 7), 16) * factor;
            return `rgb(${Math.floor(r)}, ${Math.floor(g)}, ${Math.floor(b)})`;
        }
        document.getElementById("hamsterPreview").addEventListener("load", function () {
            const svgHamster = this.contentDocument;
            if (svgHamster) {
                let color1 = "<?php echo $color1; ?>";
                let color2 = "<?php echo $color2; ?>";
                let shadow1 = darkenColor(color1);
                let shadow2 = darkenColor(color2);

                ["buttColor", "faceColor", "earColor"].forEach(id => {
                    let el = svgHamster.getElementById(id);
                    if (el) el.setAttribute("fill", color1);
                });
                ["buttS", "faceS", "earS"].forEach(id => {
                    let el = svgHamster.getElementById(id);
                    if (el) el.setAttribute("fill", shadow1);
                });
                ["chestS"].forEach(id => {
                    let el = svgHamster.getElementById(id);
                    if (el) el.setAttribute("fill", shadow2);
                });
                ["chestColor", "earFluff"].forEach(id => {
                    let el = svgHamster.getElementById(id);
                    if (el) el.setAttribute("fill", color2);
                });


            }
        });
    </script>
    <div id="cartArea" class="z-50 bottom-0 right-0 absolute w-[200px] h-[150px]"></div>
</div>