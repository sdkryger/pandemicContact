@extends('layouts.app')

@section('content')
<div class="container">
  <div class="card">
    <div class="card-header">
      Overview
    </div>
    <div class="card-body">
      <div class="h3">
        What is it?
      </div>
      <ul class="list-group">
        <li class="list-group-item">
          Demonstration of basic contact record management in times of pandemic
        </li>
        <li class="list-group-item">
          Conversation/discussion starter 
            <ul>
              <li> How can we use technology and prevalent internet connectivity to reduce the spread of a pandemic? </li>
              <li> How can we use technology to reduce the disruption/impact of a pandemic?</li>
            </ul>
        </li>
      </ul>

      <div class="h3 mt-3">
        What it's not
      </div>
      <ul class="list-group">
        <li class="list-group-item">
          Complete and finished product
        </li>
        <li class="list-group-item">
          Secure - no security implemented
        </li>
      </ul>

      <div class="h3 mt-3">
        Getting started
      </div>
      <ul class="list-group">
        <li class="list-group-item">
          Login with users 0 - 19 using email addresses 0@contact.com..19@contact.com and password: 'password'
        </li>
        <li class="list-group-item">
          Users 0 - 4 are in the same residence (continual contact). Users 5 - 9 are together in a different residence.
        </li>
        <li class="list-group-item">
          Feel free to create new users
        </li>
        <li class="list-group-item">
          After logging in, the user can:
            <ul>
              <li>See a table listing the total number of their contacts (direct and in-direct) for the past 5, 10 and 14 days</li>
              <li>Check to see the resulting number of contacts (direct and in-direct) if they were to contact someone</li>
              <li>Register contact with someone (create a contact record)</li>
            </ul>
        </li>
      </ul>

      <div class="h3 mt-3">
        Purposes/outcomes
      </div>
      <ul class="list-group">
        <li class="list-group-item">
          Allow people to see the number of contacts (direct and indirect) they currently have
        </li>
        <li class="list-group-item">
          Allow people to see the impact of creating another contact. 
          Some conneections may result in significant growth in their number of contacts while other connections may result in a relatively small increase in their number of connections.
        </li>
        <li class="list-group-item">
          Data to better understand the current pandemic and for research/preparation for future events.
        </li>
    </div>
  </div>
</div>
@endsection