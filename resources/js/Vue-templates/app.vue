<template>
    <div class="container py-5 fade-in">
      <!-- Account Details Section -->
      <div class="card bg-dark text-white shadow rounded-lg p-4 mb-5">
        <h2 class="h4 font-weight-bold mb-3">Account Details</h2>
        <ul class="list-group list-group-flush">
          <li class="list-group-item bg-transparent text-white">
            <!-- <strong>ID:</strong> {{ user.id }} -->
          </li>
          <li class="list-group-item bg-transparent text-white">
            <!-- <strong>Email:</strong> {{ user.email }} -->
          </li>
          <li class="list-group-item bg-transparent text-white">
            <!-- <strong>Username:</strong> {{ user.username }} -->
          </li>
        </ul>
        <div class="mt-4 text-center">
          <!-- <button class="btn btn-outline-light btn-lg px-4" @click="onUpdateAccount">Update Account</button> -->
        </div>
      </div>
  
      <!-- Main Content Section -->
      <div class="row">
        <!-- Friends List -->
        <div class="col-md-6 mb-4">
          <div class="card bg-dark text-white shadow rounded-lg p-4 h-100">
            <h2 class="h4 font-weight-bold mb-3">Friends</h2>
            <ul class="list-group list-group-flush">
              <li class="list-group-item bg-transparent text-white">
                <!-- <app-friend :friendArrayItem="friend" /> -->
              </li>
            </ul>
          </div>
        </div>
        
        <!-- Pending Friend Requests -->
        <div class="col-md-6 mb-4">
          <div class="card bg-dark text-white shadow rounded-lg p-4 h-100">
            <h2 class="h4 font-weight-bold mb-3">Pending Friend Requests</h2>
            <ul class="list-group list-group-flush">
              <li class="list-group-item bg-transparent text-white">
                <!-- <app-friend-request-card
                  :friendRequest="friend"
                  :isLoading="isLoadingFriendRequestResponse"
                  @requestRespond="respondToFriendRequest"
                /> -->
              </li>
            </ul>
          </div>
        </div>
  
        <!-- Add New Friends -->
        <div class="col-12">
          <div class="card bg-dark text-white shadow rounded-lg p-4">
            <h2 class="h4 font-weight-bold mb-3">Add New Friends</h2>
            <ul class="list-group list-group-flush">
              <!-- <li v-for="(user, index) in usersThatHaveNotBeenFriended" :key="index" class="list-group-item bg-transparent text-white">
                <app-sidebar-add-friend :user="user" @addFriendByUserId="addFriend" />
              </li> -->
            </ul>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <style scoped>
  .container {
    max-width: 900px;
    margin: auto;
  }
  .card {
    border-radius: 15px;
  }
  .list-group-item {
    border: none;
  }
  .btn-outline-light {
    transition: all 0.3s ease-in-out;
  }
  .btn-outline-light:hover {
    background: white;
    color: black;
  }
  </style>
  
  
  <script>
  export default {
    data() {
      return {
        message: 'Hello from Vue Component!'
      };
    }
  };
  </script>