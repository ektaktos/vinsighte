<template>
  <div class="table-responsive">
      <table class="table">
        <tr>
          <th>sn</th>
          <th>Image</th>
          <th>status</th>
          <th>Processed Data</th>
          <th>Processed Date</th>
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
      console.log(this.pending);
      if (this.pending > 0) {
        this.getLogs();
      }
    }, 60000);
  },
  methods: {
    async getLogs(){
      try {
        const res = await axios.get('/logs/fetch');
        this.logs = res.logs;
        this.pending = res.pendingJobs;
      } catch (err){
        console.log(err);
      }
    }
  }
}
</script>

<style>

</style>