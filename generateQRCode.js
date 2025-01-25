
        function generateRandomArrays(numberOfArrays, arrayLength) {
            const arrays = [];
            for (let i = 0; i < numberOfArrays; i++) {
                const arr = [];
                for (let j = 0; j < arrayLength; j++) {
                    arr.push(Math.round(Math.random()));
                }
                arrays.push(arr);
            }
            return arrays;
        }

        function createQRCode(array, label) {
            const container = document.createElement("div");

            const labelElement = document.createElement("div");
            labelElement.textContent = label;
            container.appendChild(labelElement);

            const qrCodeElement = document.createElement("div");
            const canvas = document.createElement("canvas");
            canvas.width = 260; // 13 columns * 20px
            canvas.height = 200; // 10 rows * 20px
            qrCodeElement.appendChild(canvas);
            const ctx = canvas.getContext("2d");

            array.forEach((value, index) => {
                const x = (index % 13) * 20;
                const y = Math.floor(index / 13) * 20;
                ctx.fillStyle = value === 1 ? "black" : "white";
                ctx.fillRect(x, y, 20, 20);
            });

            container.appendChild(qrCodeElement);
            return container;
        }

        function downloadAllQRCodes() {
            const qrCodeContainers = document.querySelectorAll('#output > div');
            qrCodeContainers.forEach(container => {
                const canvas = container.querySelector('canvas');
                const labelElement = container.querySelector('div');
                const label = labelElement.textContent;
                const link = document.createElement('a');
                link.href = canvas.toDataURL("image/jpeg").replace("image/jpeg", "image/octet-stream");
                link.download = `${label}.jpg`;
                link.click();
            });
        }

        document.addEventListener("DOMContentLoaded", () => {
            const arrayLength = 100; // Length of each array (10x10 grid for simplicity)

            const output = document.getElementById("output");

            for (let i = 101; i <= 110; i++) {
                for (let j = 1; j <= 13; j++) {
                    const array = generateRandomArrays(1, arrayLength)[0]; // Generate one array
                    const label = `CMT ${i} Lecture ${j}`;
                    const qrCodeElement = createQRCode(array, label);
                    output.appendChild(qrCodeElement);
                }
            }
        });








