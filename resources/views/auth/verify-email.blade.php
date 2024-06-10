<x-layout>

    <h1 class="mb-4">Please verify your email with the email we have sent to you</h1>

    <p>Didn't get the email?</p>
    <form action="{{ route('verification.send') }}" method="POST">
        @csrf
        
        <button class="btn">Send again</button>
    </form>
    

</x-layout>