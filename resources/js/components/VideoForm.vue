<template>
<div class="col-12 col-lg-4 mb-3">
    <div class="card">
        <div class="card-header">YouTube Chunker</div>
        <div class="card-body">
                <form id="form" class="form" @submit.prevent="formSubmit">
                    <div class="alert" :class="alert.type === 'error' ? 'alert-danger' : 'alert-success'" role="alert" v-if="alert.type">
                        <span v-if="typeof alert.content === 'string'">
                            {{ alert.content }}
                        </span>
                        <span v-else v-for="line in alert.content">
                            <span v-if="typeof line === 'string'">- {{ line }}<br></span>
                            <span v-else><span v-for="item in line">- {{ item }}<br></span></span>
                        </span>
                    </div>

                    <div class="form-group">
                        <label for="fld_code">Youtube video URL</label>
                        <input type="text" class="form-control" id="fld_code" v-model="url" @input="hideAlertHandle">
                    </div>
                    <div class="form-group col-6 row">
                        <label for="fld_from" class="control-label">From (hh:mm:ss)</label>
                        <input type="text" placeholder="00:00:00" class="form-control" id="fld_from" v-model="from" @input="hideAlertHandle">
                    </div>
                    <div class="form-group col-6 row">
                        <label for="fld_to" class="control-label">To (hh:mm:ss)</label>
                        <input type="text" placeholder="00:00:00" class="form-control" id="fld_to" v-model="to" @input="hideAlertHandle">
                    </div>
                    <button type="submit" class="btn btn-primary" id="btn_submit">Submit</button>
                </form>
        </div>
    </div>
</div>
</template>

<script>
    export default {
        props: ['alert'],

        data() {
            return {
                url: '',
                from: '',
                to: '',
            }
        },

        methods: {
            formSubmit() {
                this.$emit('formsent', {
                    url: this.url,
                    from: this.from,
                    to: this.to,
                });
                this.url = '';
                this.from = '';
                this.to = '';
            },
            hideAlertHandle() {
                this.$emit('hidealert');
            }
        },
    }
</script>
