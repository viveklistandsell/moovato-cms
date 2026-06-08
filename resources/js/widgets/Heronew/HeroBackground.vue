<script setup lang="ts">
/**
 * Full-screen animated shader stack for the hero (Vue adapter of `shaders`).
 * A single <Shader> root owns the renderer. The two texture generators
 * (Swirl + ChromaFlow) are the base layer; the effect shaders (FlutedGlass,
 * FilmGrain) sample their nested children, so generators live *inside* the
 * effects for the distortion and grain to apply to them.
 */
const fill = 'absolute inset-0 w-full h-full';
</script>

<template>
    <div class="pointer-events-none absolute inset-0 z-10">
        <Shader :class="fill">
            <FilmGrain :strength="0.05">
                <FlutedGlass
                    :aberration="0.61"
                    :angle="31"
                    :frequency="8"
                    :highlight="0.12"
                    :highlightSoftness="0"
                    :lightAngle="-90"
                    :refraction="4"
                    shape="rounded"
                    :softness="1"
                    :speed="0.15"
                >
                    <Swirl colorA="#ffffff" colorB="#f0f0f0" :detail="1.7" />
                    <ChromaFlow
                        baseColor="#ffffff"
                        downColor="#ff5f03"
                        leftColor="#ff5f03"
                        rightColor="#ff5f03"
                        upColor="#ff5f03"
                        :momentum="13"
                        :radius="3.5"
                    />
                </FlutedGlass>
            </FilmGrain>
        </Shader>
    </div>
</template>
