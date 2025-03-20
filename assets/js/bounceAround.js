function bounceAround(element) {
    function move() {
        const randomX = (Math.random() - 0.5) * 20; // Random small movement
        const randomY = (Math.random() - 0.5) * 20;

        element.style.transform = `translate(${randomX}px, ${randomY}px)`;
        element.style.transition = "transform 0.5s ease-in-out";

        setTimeout(move, 800 + Math.random() * 1000); // Varying interval for a natural effect
    }
    move();
}