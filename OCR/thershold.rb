require 'chunky_png'

if (ARGV.size >= 2) then
	imagefile = ARGV[0]
	thers = ARGV[1]
else
	puts "Use: thershold imagefile thers"
	exit
end

class ChunkyPNG::Image
  def at(x,y)
    ChunkyPNG::Color.to_grayscale_bytes(self[x,y]).first
  end
end

print "Caricando immagine... "
img = ChunkyPNG::Image.from_file(imagefile+'.png')
puts "OK"

edge = ChunkyPNG::Image.new(img.width, img.height, ChunkyPNG::Color::TRANSPARENT)

print "Applying thershold... "
for x in 1..img.width-2
  for y in 1..img.height-2
    if (img.at(x,y) > Integer(thers)) then
	    edge[x,y] = ChunkyPNG::Color.grayscale(255)
		else
			edge[x,y] = ChunkyPNG::Color.grayscale(0)
		end
  end
end

puts "OK"
edge.save(imagefile+'_thers.png')
