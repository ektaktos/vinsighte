<template>
  <div class="table-responsive">
      <table class="table">
        <tr>
          <th class="table-col-1">sn</th>
          <th class="table-col-2">Image</th>
          <th class="table-col-2">Status</th>
          <th class="table-col-3">Processed Data</th>
          <th class="table-col-4">Entry Time</th>
        </tr>
        <tbody v-if="logs.length">
          <tr v-for="(log, index) in logs" :key="index">
            <td> {{ index + 1 }}</td>
            <td> <a :href="log.image_url " target="_blank">Image</a> </td>
            <td> {{ log.status }} </td>
            <td> {{ log.processed_data}} </td>
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
</template>

<script>
export default {
  props: ['joblogs', 'pendingjobs'],
  data() {
    return {
      logs: this.joblogs,
      pending: this.pendingjobs
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
    }
  }
}
</script>

<style scoped>
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