<script lang="ts">
    // MovingBackgroundShapes.svelte

    import { onMount } from "svelte";

    const shapes = [
        { shape: "square", el: null },
        { shape: "square", el: null },
        { shape: "circle", el: null },
        { shape: "circle", el: null },
        { shape: "triangle", el: null },
        { shape: "triangle", el: null },
    ];

    const random_between = (min:number, max:number) => {
        return Math.floor(Math.random() * (max - min + 1) + min);
    }

    const random_color = () => {
        const r = random_between(150, 255);
        const g = random_between(0, 50);
        const b = random_between(0, 100);
        const a = random_between(50, 100) + "%";
        return `rgba(${r}, ${g}, ${b}, ${a})`;
    }

    const random_shape = () => {
        const shapes = ["square", "circle", "triangle"];
        return shapes[random_between(0, shapes.length - 1)];
    }

    const do_shape = (shape:HTMLElement) => {
        shape.style.setProperty("--top", `${random_between(0, 100)}%`);
        shape.style.setProperty("--left", `${random_between(0, 100)}%`);
        shape.style.setProperty("--size", `${random_between(50, 200)}px`);
        shape.style.setProperty("--color", random_color());
        shape.style.setProperty("--rotate", `${random_between(0, 360)}deg`);
        shape.classList.remove("square", "circle", "triangle");
        shape.classList.add(random_shape());
        const duration = random_between(5, 10);
        setTimeout(() => {
            if(!shape) return;
            do_shape(shape);
        }, duration * 1000);
    }

    onMount(() => {
        for(let i = 0; i < shapes.length; i++) {
            do_shape(shapes[i].el)
        }
    });
</script>

<div class="moving-background-shapes">
    {#each shapes as shape, i}
        <div class="shape {shape.shape}" bind:this={shapes[i].el} />
    {/each}
</div>

<style lang="scss">
    .shape {
        position: absolute;
        top: var(--top);
        left: var(--left);
        width: var(--size);
        height: var(--size);
        background-color: var(--color);
        transform: translate(-50%, -50%) rotate(var(--rotate));
        transform-origin: center;
        transition: all 5s ease;
        border-radius:0;
    }
    .square {
        clip-path: polygon(0% 0%, 100% 0%, 100% 100%, 0% 100%);

    }
    .circle {
        clip-path: polygon(0% 0%, 100% 0%, 100% 100%, 0% 100%);
        border-radius: 50%;
    }   
 
    .triangle {
        clip-path: polygon(50% 0%, 0% 100%, 100% 100%);
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
            @apply bg-slate-800 bg-opacity-20;
            backdrop-filter: blur(20px);
            z-index: 1;
        }
    }
</style>
