<template>
  <div class="row">
    <div class="col">
      <div class="h3">
        My id: {{user.id}}
      </div>

      <div class="h3 block">
        Contacts
      </div>
      <table class="table table-sm">
        <thead>
          <tr>
            <th scope="col">Time</th>
            <th scope="col">Number of contacts</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Last 14 days</td>
            <td>{{myContacts.now}}</td>
          </tr>
          <tr>
            <td>Last 10 days</td>
            <td>{{myContacts.inFiveDays}}</td>
          </tr>
          <tr>
            <td>Last 5 days</td>
            <td>{{myContacts.inTenDays}}</td>
          </tr>
        </tbody>
      </table>
      <div class="card">
        <div class="card-header">
          Check contact or create contact record
        </div>
        <div class="card-body">
          <div class="form-group">
            <label>Id</label>
            <input type="text" v-model="contactId" class="form-control">
            <span class="text-danger" role="alert" v-if="error != ''">
              <strong>{{ error }}</strong>
            </span>
            <span class="text-success" role="alert" v-if="createContactSuccess">
              <strong>Contact record created. Thank you</strong>
            </span>
            <span class="text-info" role="alert" v-if="checkContactSuccessMessage != ''">
              <strong>{{checkContactSuccessMessage}}</strong>
            </span>
            <table v-if="contactCheckTable.length>0" class="table table-sm">
              <thead>
                <tr>
                  <th scope="col">Time</th>
                  <th scope="col">Number of contacts</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="row in contactCheckTable">
                  <td>{{row.title}}</td>
                  <td>{{row.data}}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="form-group form-check">
            <input type="checkbox" class="form-check-input" id="sameResidence" v-model="sameResidence">
            <label class="form-check-label" for="sameResidence">Same residence as me</label>
          </div>
          <div class="form-group" v-if="demo">
            <label>Contact date in format YYYY-MM-DD (blank to use current date)</label>
            <input type="text" v-model="contactDate" class="form-control">
            <span class="text-danger" role="alert">
              <strong>Only for demo purposes - to create 'old' contact records</strong>
            </span>
          </div>
        </div>
        <div class="card-footer">
          <div class="btn btn-primary" @click="checkContact">Check contact</div>
          <div class="btn btn-primary" @click="createContact">Create contact</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  export default{
    data(){
      return{
        message:'contact',
        contactId:'',
        contactDate:'',
        error:'',
        createContactSuccess:false,
        checkContactSuccessMessage:'',
        sameResidence:false,
        myContacts:{},
        demo:true,
        contactCheckTable:[]
      }
    },
    props:['user','csrf'],
    mounted(){
      this.updateMyContacts();
    },
    methods:{
      checkContact(){
        var self = this;
        self.createContactSuccess = false;
        if(parseInt(this.contactId) > 0){
          self.error = '';
          $.get(
            '/contacts/check',
            {
              contactId:this.contactId,
              _token:this.csrf
            },
            function(data){
              //console.log(JSON.stringify(data));
              if(data.error){
                self.error = data.message;
                self.checkContactSuccessMessage = ''
              }else{
                self.error = '';
                self.contactCheckTable = [
                  {
                    title:'Last 14 days',
                    data: data.ourContacts.length
                  },
                  {
                    title:'Last 10 days',
                    data: data.ourContactsLastTenDays.length
                  },
                  {
                    title:'Last 5 days',
                    data: data.ourContactsLastFiveDays.length
                  }
                ];
                self.checkContactSuccessMessage = "You have "+data.myContacts.length+" contacts. They have "+data.theirContacts.length+" contacts. Together you would have "+data.ourContacts.length+" contacts.";
              }
              
            },
            'json'
          );
        }else{
          self.error = 'Invalid contact id';
        }
      },
      createContact(){
        var self = this;
        self.checkContactSuccessMessage = '';
        if(parseInt(this.contactId) > 0){
          self.error = '';
          $.get(
            '/contacts/create',
            {
              contactId:this.contactId,
              sameResidence:this.sameResidence,
              contactDate:this.contactDate,
              _token:this.csrf
            },
            function(data){
              //console.log(JSON.stringify(data));
              if(data.error){
                self.error = data.message;
                self.createContactSuccess = false;
              }else{
                self.error = '';
                self.createContactSuccess = true;
                self.updateMyContacts();
                self.contactCheckTable = [];
              }
              
            },
            'json'
          );
        }else{
          self.error = 'Invalid contact id';
        }
      },
      updateMyContacts(){
        var self = this;
        $.get(
          '/contacts/mine',
          function(data){
            console.log(JSON.stringify(data));
            self.myContacts = {
              now:data.myContactIds.length,
              inFiveDays:data.myContactIdsLastTenDays.length,
              inTenDays:data.myContactIdsLastFiveDays.length
            }
          },
          'json'
        );
      }
    }
  }
</script>