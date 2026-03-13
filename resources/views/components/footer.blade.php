<footer class="bg-white border-t border-gray-100 pt-20 pb-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-center gap-8 mb-16">
            <div class="flex items-center gap-3">
                <x-app-logo :withTagline="false" class="!gap-2" />
                <span class="text-lg font-bold text-gray-900 tracking-tight">NBTI Market Hub</span>
            </div>
            <div class="flex items-center space-x-10 text-sm font-medium text-gray-500">
                <a href="#" class="hover:text-hub-green transition-colors">About NBTI</a>
                <a href="#" class="hover:text-hub-green transition-colors">Join the Cluster</a>
            </div>
            <div class="text-sm text-gray-400 font-medium">
                &copy; {{ date('Y') }} National Board for Technology Incubation
            </div>
        </div>
    </div>
</footer>
