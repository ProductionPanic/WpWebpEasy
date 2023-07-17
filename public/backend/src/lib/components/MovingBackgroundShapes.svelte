<script>
    // MovingBackgroundShapes.svelte

    import { onMount } from "svelte";

    let square_el = null;
    let circle_el = null;
    let triangle_el = null;

    const shapes = [
        { state: null, shape: "square", el: null },
        { state: null, shape: "square", el: null },
        { state: null, shape: "circle", el: null },
        { state: null, shape: "circle", el: null },
        { state: null, shape: "triangle", el: null },
        { state: null, shape: "triangle", el: null },
    ];

    const random_screen_pos = () => {
        const screen_width = window.innerWidth;
        const screen_height = window.innerHeight;

        const random_x = Math.floor(Math.random() * screen_width);
        const random_y = Math.floor(Math.random() * screen_height);

        return { x: random_x, y: random_y };
    };

    const random_rotation = () => {
        const random_rotation = Math.floor(Math.random() * 360);

        return random_rotation;
    };

    const random_size = (min = 10, max = 50) => {
        min = (window.innerWidth / 100) * min;
        max = (window.innerWidth / 100) * max;

        const random_size = Math.floor(Math.random() * (max - min + 1) + min);

        return random_size;
    };

    const random_color = () => {
        const random_red = Math.floor(Math.random() * 50 + 205);
        const random_green = Math.floor(Math.random() * 50);
        const random_blue = Math.floor(Math.random() * 50);
        const random_alpha = Math.random() * 0.4 + 0.6;
        const switchCols = Math.random() > 0.5;
        const colors = [
            switchCols ? random_red : random_blue,
            random_green,
            switchCols ? random_blue : random_red,
            random_alpha,
        ];

        return `rgba(${colors.join(",")})`;
    };

    const move_shape = (shape) => {
        if (!shape.state) {
            const pos = random_screen_pos();
            const rotation = random_rotation();
            const size = random_size();
            const color = random_color();
            const initial_state = {
                x: pos.x,
                y: pos.y,
                rotation: rotation,
                size: size,
                color: color,
            };
            shape.el.style.backgroundColor = color;
            shape.state = initial_state;
        }
        // start lerp
        lerp(shape, random_screen_pos(), random_rotation(), random_size());
    };

    const lerp_value = (start, end, amount) => {
        return start * (1 - amount) + end * amount;
    };

    const lerp = (shape, pos, rotation, size) => {
        const element = shape.el;

        const start_pos = {
            x: shape.state.x,
            y: shape.state.y,
        };

        const threshold = 10;

        const diff = {
            x: Math.abs(start_pos.x - pos.x),
            y: Math.abs(start_pos.y - pos.y),
        };

        if (diff.x < threshold && diff.y < threshold) {
            setTimeout(() => {
                move_shape(shape);
            }, Math.random() * 1000 * 5);
            return;
        }

        const new_pos = {
            x: lerp_value(start_pos.x, pos.x, 0.005),
            y: lerp_value(start_pos.y, pos.y, 0.005),
        };

        const new_rotation = lerp_value(shape.state.rotation, rotation, 0.005);
        const new_size = lerp_value(shape.state.size, size, 0.005);

        element.style.transform = `rotate(${new_rotation}deg) translate(-50%, -50%)`;
        element.style.width = `${new_size}px`;
        element.style.height = `${new_size}px`;
        element.style.left = `${new_pos.x}px`;
        element.style.top = `${new_pos.y}px`;

        shape.state = {
            x: new_pos.x,
            y: new_pos.y,
            rotation: new_rotation,
            size: new_size,
        };

        requestAnimationFrame(() => {
            lerp(shape, pos, rotation, size);
        });
    };

    onMount(() => {
        for (let i = 0; i < shapes.length; i++) {
            move_shape(shapes[i]);
        }
    });
</script>

<div class="moving-background-shapes">
    {#each shapes as shape, i}
        <div class={shape.shape} bind:this={shapes[i].el} />
    {/each}
</div>

<style lang="scss">
    .square {
        position: absolute;
        background-color: #f00;
    }

    .circle {
        position: absolute;
        background-color: #0f0;
        clip-path: circle(50%);
    }

    .triangle {
        position: absolute;
        clip-path: polygon(50% 0%, 0% 100%, 100% 100%);
        background-color: #00f;
    }

    .moving-background-shapes {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;

        > * {
            transform-origin: center;
        }

        &:before {
            position: absolute;
            content: "";
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            @apply bg-slate-800 bg-opacity-25;
            backdrop-filter: blur(20px);
            z-index: 1;
        }
    }
</style>
