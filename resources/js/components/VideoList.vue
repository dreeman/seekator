<template>
<div class="col">
    <div class="card">
        <div class="card-header">Your files</div>
        <div class="card-body">
            <table class="table">
                <tbody id="myfiles">
                <tr v-for="file in files">
                    <td style="width: 100px;">
                        <img class="video-thumb" :src="file.thumb">
                    </td>
                    <td>
                        <a :href="file.link" target="_blank" v-if="file.status === 0">{{ file.title }}</a>
                        <div v-else>
                            <span class="badge" :class="file.bage.type">{{ file.bage.text }}</span>&nbsp;&nbsp;&nbsp;{{ file.title }}
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
</template>

<script>
    export default {
        props: ['files'],
    };
</script>
