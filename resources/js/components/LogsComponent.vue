<template>
  <div>
    <div class="overlay" v-if="show_raw"></div>
    <div class="table-responsive container-fluid">
      <h4 class=" mt-3">Logs</h4>
      <table class="table">
        <tr>
          <th class="table-col-1">sn</th>
          <th class="table-col-2">File</th>
          <th class="table-col-2">Status</th>
          <th class="table-col-3">Processed Data</th>
          <th class="table-col-4">Entry Time</th>
        </tr>
        <tbody v-if="logs.length">
          <tr v-for="(log, index) in logs" :key="index" @click="showLog(log.raw_data)">
            <td> {{ index + 1 }}</td>
            <td> <a :href="log.file_url " target="_blank">{{ log.filename }}</a> </td>
            <td> {{ log.status }} </td>
            <td style="white-space: nowrap; text-overflow:ellipsis; overflow: hidden; max-width:1px;"> {{ log.processed_data}} </td>
            <td> {{ log.created_at | formatDate}} </td>
          </tr>
        </tbody>
        <tbody v-else>
          <tr>
            <td colspan="4" align="center"> No Records yet</td>
          </tr>
        </tbody>
      </table>
    </div>
    <log-component :jsonsource="raw_data" v-if="show_raw" @close="closeModal"></log-component>
  </div>
</template>

<script>
import LogComponent from './LogComponent.vue';
export default {
  components: {
    LogComponent
  },
  props: ['joblogs', 'pendingjobs'],
  data() {
    return {
      logs: this.joblogs,
      pending: this.pendingjobs,
      show_raw: false,
      raw_data: [],
    }
  },
  created() {
    setInterval(() => {
      if (this.pending > 0) {
        this.getLogs();
      }
    }, 5000);
  },
  methods: {
    async getLogs(){
      try {
        const res = await axios.get('/logs/fetch');
        this.logs = res.data.logs;
        this.pending = res.data.pendingJobs;
      } catch (err){
        console.log(err);
      }
    },
    showLog(data){
      this.show_raw = true;
      this.raw_data = JSON.parse(data);
    },
    closeModal(){
      this.show_raw = false;
    }
  }
}
</script>

<style scoped>
  .table-responsive{
    position: relative;
    cursor: pointer;
  }
  .overlay{
    position: fixed;
    top: 0;
    min-height: 100vh;
    width: 100%;
    background-color: rgba(0, 0, 0, 0.75);
    z-index: 3;
  }
  .table-col-1{
    width: 3%;
  }
  .table-col-2{
    width: 7%;
  }
  .table-col-3{
    width: 70%;
  }
  .table-col-4{
    width: 13%;
  }
</style>