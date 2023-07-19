<script lang="ts">
    import { fly, slide } from "svelte/transition";
    import { ImagesHandler } from "../ts/Images";
    import { StatsHandler } from "../ts/Stats";

    let images = [];
    let batch_size = 1;
    let fetch_threshold = 50;
    let images_left = true;
    let running = false;

    const convert_queue = async () => {
        if(!running) return;
        if(images.length < fetch_threshold && images_left) {
            let _imgs = await ImagesHandler.get_images(50, images.length);
            images = [...images, ..._imgs];
            if(_imgs.length < 50) images_left = false;
        }

        const batch = images.splice(0, batch_size);   
        const batch_ids = batch.map(img => img.id);
        const converted = await ImagesHandler.convert_images(batch_ids);

        StatsHandler.updateStats();

        if(images.length > 0) {
            setTimeout(convert_queue, 1000);
        } else {
            running = false;
        }
    }

    const start = async (all:boolean = false) => {
        if(running) return;
        running = true;

        if(all) {
            images = await ImagesHandler.get_images(50, 0);
            images_left = true;
        }

        convert_queue();



    }
</script>

<section class="webp-convert-app-screen">
    {#if !running }
    <div class="webp-convert-actions" transition:slide>
        <div class="actions">
            <div class="action">
                <button class="btn btn-one" on:click={() => start(true)}>
                    Convert all images
                </button>
            </div> 
        </div>
    </div>
    {/if}

    {#if running }
    <div class="webp-convert-actions" transition:slide>
        <div class="actions-title">
            <h2>
                Converting images...
            </h2>
            <div class="grid grid-cols-6 p-4 gap-2">                
            {#each images as image}
                <img 
                    src={image.thumbnail_url} 
                    alt={image.name} 
                    in:fly={{x:-100, duration: 300}}
                    out:fly={{x:100, duration: 300}}
                />
            {/each}
            </div>
        </div>
        <div class="actions">
            <div class="action">
                <button class="btn btn-one" on:click={() => start(true)}>
                    Stop processing
                </button>
            </div>
        </div>
    </div>
    {/if}
</section>


<style lang="scss">
    .webp-convert-app-screen {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        height: 100%;
        width: 100%;
        background: #f3f4f644;
        backdrop-filter: blur(10px);

        @apply my-6 pt-4;
    }

    .webp-convert-actions {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        width: 100%;
    }

    .actions-title {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        width: 100%;
    }

    .actions-title h2 {
        font-size: 1.5rem;
        font-weight: 600;
        color: #1f2937;
    }

    .actions {
        display: flex;
        flex-direction: row;
        justify-content: center;
        align-items: center;
        width: 100%;
    }

    .action {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        width: 100%;
        margin: 1rem;
    }

    .btn {
        display: inline-block;
        padding: 0.5em 1em;
        text-decoration: none;
        border-radius: 3px;
        transition: 0.2s;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        width: 100%;
        height: 100%;
    }

    .btn-one {
        color: #fff;
        border: 2px solid #1f2937;
        background: #1f2937;
    }

    .btn-one:hover {
        color: #1f2937;
        background: #fff;
    }

    .btn-two {
        color: #fff;
        border: 2px solid #1f2937;
        background: #1f2937;
    }

    .btn-two:hover {
        color: #1f2937;
        background: #fff;
    }


</style>